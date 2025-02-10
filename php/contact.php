<?php
require('routeros_api.class.php');

$API = new RouterosAPI();

$routerIP = "139.59.74.160"; // Change to your MikroTik router IP
$username = "email"; // MikroTik username
$password = "Email@898"; // MikroTik password


// Check if form data exists
$name = isset($_POST['name']) ? $_POST['phone'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$message = isset($_POST['comments']) ? $_POST['comments'] : null;


$recipient = "treeohotels25@gmail.com"; // Change to recipient's email
$subject = "Contact Form Submission";
$body = "You have received a new message from the contact form:\n\n".
		"Name: $name\n\n".
        "Email: $email\n".
        "Message:\n$message";

if ($API->connect($routerIP, $username, $password, 8736)) {
    
    // Send email command to MikroTik
    $API->write('/tool/e-mail/send', false);
    $API->write("=to={$recipient}", false);
    $API->write("=subject={$subject}", false);
    $API->write("=body={$body}", true);
    
    $API->read();
    
    echo "Email sent successfully!";
    header("Location: /contact-01.html?status=success");
    
    $API->disconnect();
} else {
    echo "Failed to connect to MikroTik API!";
    header("Location: /contact-01.html?status=error");

}
?>
