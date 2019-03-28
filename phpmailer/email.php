<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

function sendEmail($email,$subject,$body) {

	$mail = new PHPMailer(true);  	                          // Passing `true` enables exceptions

	$sender_mail = "sly14flores@gmail.com";
	
	try {
		//Server settings
		// $mail->SMTPDebug = 2;                                 // Enable verbose debug output
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';						  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'sly14flores@gmail.com'; #          // SMTP username
		$mail->Password = 'iamslylegend';          #          // SMTP password
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                               	  // TCP port to connect to

		//Recipients
		$mail->setFrom($sender_mail, 'iTrack (Document Tracking System)');
		$mail->addAddress($email, $email); // Add a recipient
		$mail->addReplyTo($sender_mail, 'iTrack');
		$mail->addCC('sly@christian.com.ph');
		// $mail->addBCC('bcc@example.com');

		// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		
		//Content		
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->AltBody = '';
		
		$mail->send();
		return array("status"=>true);
	} catch (Exception $e) {
		return array("status"=>false,"error"=>$mail->ErrorInfo);
	};

};

?>