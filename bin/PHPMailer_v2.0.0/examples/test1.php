<?php

include_once('../class.phpmailer.php');

$mail    = new PHPMailer();

$body    = $mail->getFile('contents.html');

$body    = eregi_replace("[\]",'',$body);
$mail->Mailer = $config['socket'];
$mail->Host = $config['host'];
$mail->SMTPAuth = $config['smtp_auth'];
$mail->Username = $config['user_name'];
$mail->Password = $config['password'];
$mail->Timeout = (double) $config['timeout'];
$mail->From     = "name@yourdomain.com";
$mail->FromName = "First Last";

$mail->Subject = "PHPMailer Test Subject";

$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML($body);

$mail->AddAddress("whoto@otherdomain.com", "John Doe");

if(!$mail->Send()) {
  echo 'Failed to send mail';
} else {
  echo 'Mail sent';
}

?>