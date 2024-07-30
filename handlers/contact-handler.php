<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath. '/pages/includes/PHPMailer/PHPMailer.php';
require_once $rootPath. '/pages/includes/PHPMailer/SMTP.php';
require_once $rootPath. '/pages/includes/PHPMailer/Exception.php';
require_once $rootPath. '/config/config.php';
$emailSent =  false;
$emailError = "";
use PHPMailer\PHPMailer\PHPMailer;
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $email = $_POST["email"];
        $subject = $_POST["subject"];
        $message = $_POST["message"];

        $mail = new PHPMailer();

        // Configure the SMTP server (replace with your SMTP details)
        $mail->isSMTP();
        $mail->Host = SMTP_SERVER;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = 'tls'; // Use 'ssl' or 'tls' as needed
        $mail->Port = 587; // Use the appropriate port

        // Set sender and recipient
        $mail->setFrom($email, $first_name . ' ' . $last_name);
        // To address
        $mail->addAddress(TO_MAIL_ID, TO_MAIL_RECIPIENT_NAME);

        // Email subject and body
        $mail->Subject = 'Contact Form Submission';

        // Format email content in a table
        $emailContent = "<table border='0'>";
        $emailContent .= "<tr><td><b>First Name:</b></td><td>$first_name</td></tr>";
        $emailContent .= "<tr><td><b>Last Name:</b></td><td>$last_name</td></tr>";
        $emailContent .= "<tr><td><b>Email:</b></td><td>$email</td></tr>";
        $emailContent .= "<tr><td><b>Subject:</b></td><td>$subject</td></tr>";
        $emailContent .= "<tr><td><b>Message:</b></td><td>$message</td></tr>";
        $emailContent .= "</table>";

        $mail->MsgHTML($emailContent);

        // Send the email
        if ($mail -> send()) {
            $emailSent = true;
            $conn = initDb();
            $sql = "INSERT INTO contact_form (first_name, last_name, email, subject, message) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $email, $subject, $message);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } else {
            $emailError = $mail->ErrorInfo;
        }
    }
} catch(Exception $e) {
    $emailError = $e -> getMessage();
}
