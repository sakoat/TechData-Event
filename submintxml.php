<?php 
  require_once("connect.php");	
	$continue = TRUE;
	while($continue === TRUE)
	{
		$sql = "select id, first_name, last_name, email, phone, city, state, tellus, post_status, originating_form_submitted from truck_driver where post_status = 'pending' limit 1";
		if(($result = mysql_query($sql)) !== FALSE && mysql_num_rows($result) > 0)
		{
            $ia = array();    # array container to hold response results
            unset($xml_string, $response_xml);
			
            $row = mysql_fetch_assoc($result);
			//print_r($row );exit;
            $sql2 = "update truck_driver set post_status = 'posting_now' where id = ".intval($row['id']);
            $result2 = mysql_query($sql2);
			
            # build the XML you need here using $row to pull your variables and insert them into your XML. Put originating_form_submitted into AppReferrer and BayardLeadForm into Source.
            $xml = new DomDocument('1.0', 'UTF-8');
			$xml->formatOutput = true;
			
			$TenstreetData = $xml->createElement("TenstreetData");
			$xml->appendChild($TenstreetData);
			$Mode=$xml->createElement("Mode", "TEST"); 
			$TenstreetData->appendChild($Mode);
			$Source=$xml->createElement("Source", "BayardLeadForm"); 
			$TenstreetData->appendChild($Source);		
			$company_id="15";
			$CompanyId = $xml->createElement("CompanyId", $company_id); 
			$TenstreetData ->appendChild($CompanyId);
			$driver_id="1001";
			$DriverId = $xml->createElement("DriverId", $driver_id); 
			$TenstreetData ->appendChild($DriverId);
			
			$PersonalData = $xml->createElement('PersonalData');
			$TenstreetData->appendChild($PersonalData);
			//Person name node
			$PersonName = $xml->createElement("PersonName");
			$PersonalData->appendChild($PersonName);
			$fname=$row['first_name'];
			$lname=$row['last_name'];
			$Prefix = $xml->createElement("Prefix"); 
			$PersonName ->appendChild($Prefix);	
			$GivenName = $xml->createElement("GivenName", htmlentities($fname)); 
			$PersonName ->appendChild($GivenName);	
			$FamilyName = $xml->createElement("FamilyName", htmlentities($lname)); 
			$PersonName ->appendChild($FamilyName);	
			
			//Address node
			//$address=$row['city'];
			//if($row['state']!="")
			//$address.=", ".$row['state'];
			$AddressCity=$row['city'];
			$AddressState=$row['state'];
			$PostalAddress = $xml->createElement("PostalAddress");
			$PersonalData->appendChild($PostalAddress);
			$CountryCode = $xml->createElement("CountryCode","US");
			$PostalAddress ->appendChild($CountryCode);

			$Municipality = $xml->createElement("Municipality", htmlentities($AddressCity));
			$PostalAddress ->appendChild($Municipality);
			$Region = $xml->createElement("Region", htmlentities($AddressState));
			$PostalAddress ->appendChild($Region);	
			//$Address1 = $xml->createElement("Address1",$address);
			//$PostalAddress ->appendChild($Address1);
			
			//Personal Date i.e. email,phone node
			$ContactData = $xml->createElement("ContactData");
			$contactAttribute = $xml->createAttribute('PreferredMethod');
			$contactAttribute->value = 'PrimaryPhone';
			$ContactData->appendChild($contactAttribute);
			$PersonalData->appendChild($ContactData);
			$email=$row['email'];
			$InternetEmailAddress = $xml->createElement("InternetEmailAddress", $email);
			$ContactData ->appendChild($InternetEmailAddress);		
			$PrimaryPhone=$row['phone'];
			$PrimaryPhone = $xml->createElement("PrimaryPhone", $PrimaryPhone); 
			$ContactData ->appendChild($PrimaryPhone);	
			
			//ApplicationData node
			$ApplicationData = $xml->createElement('ApplicationData');
			$TenstreetData->appendChild($ApplicationData);
			//AppReferrer node
			$originating_form_submitted=$row['originating_form_submitted'];
			$AppReferrer = $xml->createElement('AppReferrer',$originating_form_submitted);
			$ApplicationData->appendChild($AppReferrer);
			$Accidents = $xml->createElement("Accidents");
			$ApplicationData->appendChild($Accidents);
			$Accident = $xml->createElement("Accident");
			$Accidents->appendChild($Accident);
			$tellus=$row['tellus'];
			$Description = $xml->createElement("Description", $tellus);
			$Accident ->appendChild($Description);
			
			//DisplayFields
			$DisplayFields = $xml->createElement('DisplayFields');
			$ApplicationData->appendChild($DisplayFields);
			$DisplayField = $xml->createElement('DisplayField');
			$DisplayFields->appendChild($DisplayField);
			$DisplayId = $xml->createElement('DisplayId','driver_type');
			$DisplayField->appendChild($DisplayId);
			$DisplayPrompt = $xml->createElement('DisplayPrompt','Please tell us about any accidents or moving violations you had in the past three (3) years');
			$DisplayField->appendChild($DisplayPrompt);
			$DisplayValue = $xml->createElement('DisplayValue',$tellus);
			$DisplayField->appendChild($DisplayValue);		
			
			
			$xml_string = $xml->saveXML();
			$post_address="https://dev.dashboard.tenstreet.com/post/tenstreet_standard_post_receive.php";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$post_address); 
			curl_setopt($ch, CURLOPT_VERBOSE, 1); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
			// return into a variable curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			// allow redirects curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8')); 
			curl_setopt($ch, CURLOPT_POST, true); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_string); 
			$response_xml = curl_exec($ch); 
			// run the whole process curl_close($ch);
			
            # make sure $response_xml is XML and that status says Accepted
		
		if((@$resp_xml_obj = simplexml_load_string(trim($response_xml))) === FALSE)   # suppress any warnings if it fails to load...will return false nonetheless
		{
			# for some reason, Tenstreet's listener didn't return an XML response. This could be because of an internet outage, a site issue, etc. This almost never happens, but you should still catch the error
			
			# optionally put in some retry logic and try again later.
			
			continue;
		}
		
		
		$ia['response_subjectid'] = mysql_real_escape_string($resp_xml_obj->DriverId);
		$ia['response_result'] = mysql_real_escape_string($resp_xml_obj->Status);
		$ia['response_statusdescription'] = mysql_real_escape_string($resp_xml_obj->Description); 
		
		$ia['msg'] = "tenstreet_standard_file_poster RESPONSE RESULT IS {$ia['response_result']} SUBJECT: {$ia['response_result']} DESCRIPTION: {$ia['response_statusdescription']}";
		
		
		# Update result in table
		$sql = "update truck_driver set result = '".$ia['response_result']." - ".$ia['response_statusdescription']."', post_status = 'complete' where id = ".$row['id'];
		$result = mysql_query($sql);
		
		}
		else
		{
            # we're done - all pending rows have been processed.
			$ia['msg']="No Pending data to submit.";
            exit;
		}
	}
	mysql_close($link);	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>CRST Form</title>
		<style type="text/css">
			body{background:#00518a;}
			*{font:normal 12px Arial;color:#fff}
		</style>
	</head>	
	<body align="center">
		<div id="theform">
			<?php echo $ia['msg'];?>
		</div>	
	</body>
</html>
