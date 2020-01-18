<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



function send_new_book_mail_warning($book_id, $book_name, $book_author, $pdf_hash, $epub_hash) {

    // Load Composer's autoloader
    require './vendor/autoload.php';

    // load secret credentials
    require './credentials.php';


    $mail = new PHPMailer(true);

	$book_folder_link = "http://www.freewriters.org/library-data/";

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                  // Enable verbose debug output
        $mail->isSMTP();                                        // Set mailer to use SMTP

        $mail->Host = $smtp_hostname;							// Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                             	// Enable SMTP authentication
        $mail->Username = $smtp_username;             			// SMTP username
        $mail->Password = $smtp_password;                   	// SMTP password

        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;     	// Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        
        // $mail->Port = 587;                                    	// STARTTLS TCP port to connect to
        $mail->Port = 465;										// SMTPS TCP port to connect to


        // Recipients
        $mail->setFrom('daniel@freewriters.org', 'FreeWriters Website');
        //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
        $mail->addAddress('daniel@freewriters.org');            // Name is optional
        $mail->addReplyTo('daniel@freewriters.org');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        // Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name


        // Content
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'New book added!';

        $message = "The library has a new book (of id: {$book_id}) that needs copyright validation.<br>The book name is {$book_name} by {$book_author}.";

        if($pdf_hash != 'null') {
        	$message .= "<br><a href=\"{$book_folder_link}{$pdf_hash}.pdf\">PDF Version</a>";
        }
        if($epub_hash != 'null') {
        	$message .= "<br><a href=\"{$book_folder_link}{$epub_hash}.epub\">EPUB Version</a>";
        }

        $mail->Body = $message;

        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();

    } catch (Exception $e) {

        $mail_error = $mail->ErrorInfo;

        echo "Email message warning saying that new book was added could not be sent automatically to the FreeWriters Library Maintainer.

Please notify us through the contact page (http://freewriters.org/contact/) so that we can validate your book and be available in our library.
Mailer Error: {$mail_error}

";
    }
}
