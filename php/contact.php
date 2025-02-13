<?php
require('routeros_api.class.php');

$API = new RouterosAPI();

$routerIP = "139.59.74.160"; // Change to your MikroTik router IP
$username = "email"; // MikroTik username
$password = "Email@898"; // MikroTik password


$recaptcha_secret = '6Lf6P9YqAAAAAHpU31JqZraEcuBp_BnJbkU5X2-e';
$recaptcha_response = $_POST['g-recaptcha-response'];

$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
$response_keys = json_decode($response, true);

if(intval($response_keys["success"]) !== 1) {
    // CAPTCHA failed
    header("Location: /contact-01.html?status=error");
    exit;
}



// Check if honeypot field is filled (spam submission)
if (!empty($_POST['password'])) {
    // Spam detected, exit the script without sending the email
    header("Location: /index.html?status=spam");
    exit;
}

// Check if form data exists
$name = isset($_POST['name']) ? $_POST['name'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$subject = isset($_POST['subject']) ? $_POST['subject'] : null;
$message = isset($_POST['comments']) ? $_POST['comments'] : null;


// Sanitize input to avoid HTML special characters breaking the email format
$name = htmlspecialchars($name);
$email = htmlspecialchars($email);
$subject = htmlspecialchars($subject);
$message = htmlspecialchars($message);


$recipient = "treeohotels25@gmail.com"; // Change to recipient's email

$body = "You have received a new message from the yellow contact us form:\n\n".
		"Name: $name\n\n".
        "Email: $email\n".
		"Subject: $subject\n".
        "Message:\n$message";

if ($API->connect($routerIP, $username, $password, 8736)) {
    
    // Send email command to MikroTik
    $API->write('/tool/e-mail/send', false);
    $API->write("=to={$recipient}", false);
    $API->write("=subject=New message from: $subject", false); 
    $API->write("=body={$body}", true);
    
    $API->read();
    
    echo "Email sent successfully!";
    header("Location: /contact-01.html?status=success");
    
    $API->disconnect();
} else {
    echo "Failed to send the mail!";
    header("Location: /contact-01.html?status=error");

}
?>
