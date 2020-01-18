<?php 

require './phpmailer.php';

send_new_book_mail_warning('book_id', 'book_name', 'book_author', 'pdf_hash', 'epub_hash');

echo "test email sent\n";
