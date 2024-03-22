<?php

session_start();
$con = mysqli_connect("localhost", "hycnpwkd", "Amit@2020#", "hycnpwkd_solar");
if($con->connect_error){
	echo "Database Connection Failed:";
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
date_default_timezone_set("Asia/Kolkata");


if($_SERVER["REQUEST_METHOD"] == "POST"){
	$name = $_POST['name'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	//$phone1 = "91".$phone;
	$city = $_POST['city'];
	$state = $_POST['state'];
	$loanamt = $_POST['loanamt'];
	$prefix = "KUSM20200PM92R-";
	
	$checkexists = $con->query("SELECT * FROM applications WHERE email = '$email ' OR phone = '$phone'");
	if($checkexists->num_rows > 0){
		$_SESSION['error'] = "Email Address/Phone Number Already Exists!";
		header("location: index.php");
		exit();
	}else{
		$getappid = $con->query("SELECT * FROM applications ORDER BY id DESC;");
		if($getappid->num_rows == 0){
			$count = "0001";
			$app_id = $prefix.$count;
		}else{
			$lid = $getappid->fetch_assoc();
			$lastid = $lid['app_id'];
			$lastvalue = explode("-",$lastid);
			$nextvalue = sprintf("%'04d", $lastvalue[1]+1);
			$app_id = $prefix.$nextvalue;
		}
		$sql = "INSERT INTO applications (app_id, name, email, phone, city, state,  loanamt, created_on) VALUES ('$app_id', '$name', '$email', '$phone', '$city', '$state',  '$loanamt', NOW())";
		$mail = new PHPMailer(true);
			
			//Server settings
			$mail->SMTPDebug = 0;                                       // Enable verbose debug output
			$mail->isSMTP();                                            // Set mailer to use SMTP
			$mail->Host       = 'mail.soralpalant.net.in';  // Specify main and backup SMTP servers
			$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		    $mail->Username   = 'noreply@soralpalant.net.in';                     // SMTP username
			$mail->Password   = 'Amit@2020';                      // SMTP password
			$mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
			$mail->Port       = 465;                                    // TCP port to connect to

			//Recipients
			$mail->setFrom('noreply@soralpalant.net.in');					########Put the Email same as Above
			$mail->addAddress('adminpanel@kusumyojanakisan.com');     						// Add a recipient
			

			// Content
			$mail->isHTML(true);                                  		// Set email format to HTML
			$mail->Subject = ' Application Recived';
			$mail->Body    = '<p>Thanks & Regards<p><p>Team Kusum Yojona</p>';
			//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			if($mail->send()){
				if($con->query($sql) == TRUE){
					
					$_SESSION['success'] = "Application Submitted Successfully! Your Application Id is: $app_id";
					header("location: index.php");
					exit();
				}else{
					$_SESSION['error'] = "Somethign went Wrong! Contact Admin";
					header("location: index.php");
					exit();
				}
			}else{
				$_SESSION['error'] = 'Please Enter correct email Id or contact Admin';
				header("location: index.php");
				exit();
			}
	}
}else{
	$_SESSION['error'] = "Forbidden Access";
	header("location: index.php");
	exit();
}
?>