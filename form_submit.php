<?php
require_once("connect.php");
$state_array["AL"] = "Alabama";
$state_array["AR"] = "Arkansas";
$state_array["GA"] = "Georgia";
$state_array["IA"] = "Iowa";
$state_array["IL"] = "Illinois";
$state_array["OH"] = "Ohio";
$state_array["MS"] = "Mississippi";
$state_array["MO"] = "Missouri";
$state_array["PA"] = "Pennsylvania";
$state_array["SC"] = "South Carolina";
$state_array["TN"] = "Tennessee";
$err = "";
$msg = "";
if (isset($_POST['submit_x'])) {
    extract($_POST);
    if ($firstname == "" || $firstname == "Your First Name")
        $err.="First name cannot be left empty.";
    if ($lastname == "" || $lastname == "Your Last Name")
        $err.="<br>Last name cannot be left empty.";
    if ($city == "" || $city == "City")
        $err.="<br>City cannot be left empty.";
    if ($state == "")
        $err.="<br>State cannot be left empty.";
    if ($interested == "")
        $err.="<br>Interested cannot be left empty.";
    if ($accidents == "YES" && $tellus == "")
        $err.="<br>Tell us cannot be left empty.";
    if ($email == "" || $email == "name@email.com")
        $err.="<br>Email cannot be left empty.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err.="Email is not a valid address.";
    }
    if ($phone == "" || $phone == "Phone Number")
        $err.="<br>Phone cannot be left empty.";
    elseif (!preg_match('/^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/', $phone)) {
        $err.="<br>Phone number is not valid. Should be in this format: xxx-xxx-xxxx";
    }
    if ($err == "") {
        $sql = "INSERT INTO truck_driver SET
  		first_name='" . mysql_real_escape_string($firstname) . "',
			last_name='" . mysql_real_escape_string($lastname) . "',
			email='" . $email . "',
			phone='" . $phone . "',
			city='" . $city . "',
			state='" . $state . "',
			tellus='" . mysql_real_escape_string($tellus) . "',
			company_id = 756,
			originating_form_submitted='BayardLeadPageLPMaloneCraigslist',
			post_status='pending',
			interested='" . $interested . "',
			accidents='" . $accidents . "'
			";

        if (mysql_query($sql)) {
            $msg = "Your entry has been submitted!";
            $subject = 'Thank you for your interest in CRST!';
            $mail_msg = "Thank you so much for your interest in CRST!<br><br>If you would like to speak to a recruiter now, please call 866-612-1746.
				<br><br>If you are ready to go through our full application process, <a href='https://intelliapp.driverapponline.com/c/crstmalone?r=craigslistmalonelp'>click here</a>.
				";
            $to = $firstname . "<" . $email . ">";
            // message
            $message = '
				<html>
				<head>			  
				</head>
				<body>
				' . $mail_msg . '
				</body>
				</html>
				';

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            // Additional headers
            //$headers .= 'To: ' . $email . ' <' . $email . '>' . "\r\n";
            $headers .= 'From:  jchapman@crst.com<jchapman@crst.com>' . "\r\n";
            //$headers .= 'Bcc: sahbaj.uddin@bglobalsourcing.com' . "\r\n";
            mail($to, $subject, $message, $headers);
            header('Location: http://crstcareers.com/malone-lp-craigslist-thank.php');
        } else {
            $msg = "Error. Please try again.";
        }

        mysql_close($link);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" >
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="language" content="en"/>
        <title>CRST Careers - crstcareers.com</title>
        <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
        <meta name="description" content="CRST Careers" />
        <meta name="keywords" content="owner operators, owner operator jobs, owner operator trucking, trucking jobs, truck driving jobs in crst, owner operator pay, jobs for owner operators, crst trucking" />
        <link rel="canonical" href="http://www.crstcareers.com/" />
        <meta name= "robots" content="all"/>
        <link rel="stylesheet" href="css/style_lp_oo.css" type="text/css" media="all" />
        <link rel="stylesheet" type="text/css" href="fonts_lp/stylesheet.css" />
        <script type="text/javascript">
	
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-30395622-1']);
            _gaq.push(['_trackPageview']);
	
            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
	
        </script>
    </head>
    <body>
        <div class="wrapper">
            <div class="maincon">
                <div class="maincon-lft">
                    <div class="header">
                        <h1>Flatbed Lease Purchase Drivers...</h1>
                        <h2>DON’T JUST <br />
                            DRIVE, <span>BELONG!</span></h2>
                    </div>
                    <div class="banner"><img src="images/banner.jpg" width="740" height="158" /></div>
                    <div class="amount">
                        <div class="lft"><span class="dolor1">$</span>700-1200 <span>Per Week Take Home</span></div>
                        <div class="rht"> Call a recruiter today and learn<br />
                            more about how we take care <br />
                            of our people. </div>
                    </div>
                    <div class="tablecon">
                        <div class="col1"><span class="dolor">$</span>1000 <span>Sign-On Bonus</span></div>
                        <div class="col2">Low Payments</div>
                        <div class="col3">100% <span>Pass-Through Fuel Surcharge</span></div>
                        <div class="col1">60<span class="cent">&#xa2;</span><span class="gal">/gal</span> <span>Top Fuel Discount</span></div>
                        <div class="col2">Late Model <span>Freightliners</span></div>
                        <div class="col3">No Credit Check <span>No Money Down</span> </div>
                        <div class="row">Call and see if your military experience will transfer to Malone</div>
                    </div>
                </div>

                <!--End left container
            ========================================================-->

                <div class="maincon-rht">
                    <div class="frmcon"> <img src="images/logo.jpg" width="200" height="64" />
                        <h2>866-612-1746</h2>
                        <p>Or for more information, <br />
                            fill out the form below</p>
                        <?php if (isset($err)) echo "<p style='color:red;font-weight:700;'>" . $err . "</p>"; ?>
                        <form action="malone-lp-craigslist.php" method="post" name="car">
                            <label>First Name:</label>
                            <input type="text" name="firstname" id="firstname" value="Your First Name" onblur="if(this.value=='') this.value='Your First Name'" onfocus="if(this.value=='Your First Name') this.value='';" class="inputstyle" />
                            <label>Last Name:</label>
                            <input type="text" name="lastname" id="lastname" value="Your Last Name" onblur="if(this.value=='') this.value='Your Last Name'" onfocus="if(this.value=='Your Last Name') this.value='';" class="inputstyle" />
                            <label>Email:</label>
                            <input type="text" name="email" value="name@email.com" onblur="if(this.value=='') this.value='name@email.com'" onfocus="if(this.value=='name@email.com') this.value='';" class="inputstyle" />
                            <label>Phone: (no hyphen or space):</label>
                            <input type="text" name="phone" value="Phone Number" onblur="if(this.value=='') this.value='Phone Number'" onfocus="if(this.value=='Phone Number') this.value='';" class="inputstyle" />
                            <label>City:</label>
                            <input type="text" name="city" value="City" onblur="if(this.value=='') this.value='City'" onfocus="if(this.value=='City') this.value='';" class="inputstyle" />
                            <label>State:</label>
                            <select tabindex="6" name="state">
                                <option value="">--Select State--</option>
                                <?php
                                $selected = "";
                                foreach ($state_array as $key => $value) {
                                    if (isset($state) && $state == $key)
                                        $selected = 'selected="selected"';
                                    else
                                        $selected = "";
                                    echo'<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                }
                                ?>
                            </select>
                            <!--
                            <label>Do you have a valid Class A CDL?</label>
                            <input type="radio" name="CDL" value="YES" required/>
                            Yes
                            <input type="radio" name="CDL" value="NO" />
                            No
                            -->
                            <label>Do you have at least 6 months OTR or Regional Class A CDL Experience?</label>
                            <input type="radio" name="CDL_1" value="YES" required/>
                            Yes
                            <input type="radio" name="CDL_1" value="NO" />
                            No
                            <label>Are you interested in Owner Operator <br />
                                or Lease Purchase?</label>
                            <select tabindex="6" name="interested">
                                <option value="">--Select One--</option>
                                <option value="Owner Operator">Owner Operator</option>
                                <option value="Lease Purchase">Lease Purchase</option>
                            </select>
                            <label>Have you had any accidents in the past three (3) years?</label>
                            <input type="radio" name="accidents" value="YES" required/>
                            Yes
                            <input type="radio" name="accidents" value="NO" />
                            No
                            <label>If yes,please describe:</label>
                            <textarea name="tellus" id="tellus"></textarea>
                            <input type="image" name="submit" src="images/btnsubmit.png" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="inner">
                <div class="footer-lft"><img src="images/logofooter.jpg" width="232" height="75" /></div>
                <div class="footer-rht">866-612-1746 <span>www.joinmalone.com</span></div>
            </div>
        </div>
    </body>
</html>
