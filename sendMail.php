<?php

$subject = "Test Email from PHP";

$firstName = "Tymofii";
$lastName = "Koliesnikov";
$city = "Kharkiv";

$text1 = "First Name: " . $firstName . "\n";
$text2 = "Last Name: " . $lastName . "\n";
$text3 = "City: " . $city . "\n";

$message = $text1 . $text2 . $text3;

echo "============" . "\n";
echo $subject . "\n";
echo "============" . "\n";
echo $message;

// Заголовки
$headers = "From: your_email@gmail.com";

// Надсилання листа
mail("recipient_email@gmail.com", $subject, $message, $headers);

?>