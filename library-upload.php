<?php

header('Content-Type: text/plain; charset=utf-8');


// General POST form validation

// in the html form must have action="library-upload.php?processed=1"
if (!isset($_GET['processed'])) {
    exit('ERROR: Submit through the form');
}

if (empty($_POST)) {
    exit("ERROR: Maximum upload exceeded! Maximum allowed:\n\t" . '- Per file: ' . ini_get('upload_max_filesize') . "\n\t- Total files: " . ini_get('post_max_size'));
}

// CSRF Session Validation
session_start();

if (!isset($_POST['csrf_token'])) {
    exit('CSRF Attack or expired CSRF token');
}
if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    exit('CSRF Attack or expired CSRF token. Go back and try again.');
}


// Functions

require './phpmailer.php';

function reArrayFiles(&$file_post)
{
    if (is_null($file_post['name'])) {
        return [];
    }
    $file_ary = [];
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

function process_tmp_name($tmp_name_array)
{
    foreach ($tmp_name_array as $tmp_name) {
        if (empty($tmp_name)) {
            return false;
        }
    }

    return true;
}

function starts_with($haystack, $needle)
{
    return $haystack[0] === $needle[0]
    ? strncmp($haystack, $needle, strlen($needle)) === 0
    : false;
}


// May edit
$file_multiple_input_names = ['pdf-book-file', 'epub-book-file']; // input with multiple files can all have .sig keys. If one not, must change "verification if there is more then 1 number of files upload per extension (with the exception of .sig)" section control.
$file_solo_input_names = ['cover-book-file'];

$max_file_size = 10485760;
$upload_dir = './library-data/';

// add all supported types
$total_number_files_per_extension['pdf'] = 0; // need to be set to zero
$total_number_files_per_extension['epub'] = 0; // need to be set to zero
$total_number_files_per_extension['sig'] = 0; // need to be set to zero
$total_number_files_per_extension['png'] = 0; // need to be set to zero
$total_number_files_per_extension['jpg'] = 0; // need to be set to zero
$total_number_files_per_extension['bmp'] = 0; // need to be set to zero

// add book extensions that we handle and corresponding html fields (it is not the pdf-book-file[] input field name but it is pdf-book-file name)
$books_extension = [
    'pdf-book-file' => 'pdf',
    'epub-book-file' => 'epub'
];

try {
    $total_input_files_array = [];
    
    foreach ($file_solo_input_names as $name) {
        if (file_exists($_FILES[$name]['tmp_name']) && is_uploaded_file($_FILES[$name]['tmp_name'])) {
            $total_input_files_array[$name][0] = $_FILES[$name];
        }
    }

    foreach ($file_multiple_input_names as $name) {
        $tmp_name = $_FILES[$name]['tmp_name'];
        if (process_tmp_name($tmp_name)) {
            $total_input_files_array[$name] = reArrayFiles($_FILES[$name]);
        }
    }

    // in this program line we have a total_input_files_array properly formated for a foreach

    // Check if files were selected
    if (empty($total_input_files_array)) {
        throw new RuntimeException('ERROR: You must submit at least one book file.', 1);
    }

    $total_files = [];
    $sig_bool = []; // tells if there is a sig file per input field name
    // initialize sig_bool
    foreach ($books_extension as $html_input_name => $extension) {
        $sig_bool[$extension] = false;
    }

    foreach ($total_input_files_array as $input_field_name => $input_field_files) {
        $file_hash = '';
        $sig = [];
        foreach ($input_field_files as $key => $file) {
            $original_filename = $file['name'];


            // Undefined | Multiple Files | $_FILES Corruption Attack
            // If this request falls under any of them, treat it invalid.
            if (
                !isset($file['error']) ||
                is_array($file['error'])
            ) {
                throw new RuntimeException('ERROR: '.$original_filename.' -> Invalid parameters.');
            }

            // Check $file['error'] value.
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                break;
                case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('ERROR: '.$original_filename.' -> Exceeded filesize limit.');
                default:
                throw new RuntimeException('ERROR: '.$original_filename.' -> Unknown errors.');
            }

            // You should also check filesize here.
            if ($file['size'] > $max_file_size) {
                throw new RuntimeException('ERROR: '.$original_filename.' -> Exceeded filesize limit.');
            }

            // DO NOT TRUST $file['mime'] VALUE !!
            // Check MIME Type by yourself.
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $ext = array_search(
                $finfo->file($file['tmp_name']),
                [
                    'pdf' => 'application/pdf',
                    'epub' => 'application/epub+zip',
                    'sig' => 'application/octet-stream',
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'bmp' => 'image/bmp'
                ],
                true
            );
            if (false === $ext) {
                throw new RuntimeException('ERROR: '.$original_filename.' -> Invalid file format.');
            }

            // Set variables

            $total_input_files_array[$input_field_name][$key]['ext'] = $ext;


            // verification if there is more then 1 number of files upload per extension (with the exception of .sig)
            ++$total_number_files_per_extension[$ext]; // it must be outside to sigs be counted
            if ($ext !== 'sig' && $total_number_files_per_extension[$ext] > 1) {
                throw new RuntimeException('ERROR: You can only upload one '.$key.' file.');
            }

            // save sig details to create 'hash' later. Throw error if uploaded more then 1 .sig per html input file field
            if ($ext === 'sig') {
                if (!empty($sig['input_field_name'])) {
                    throw new RuntimeException('ERROR: You can only upload one .sig per book');
                }
                $sig['input_field_name'] = $input_field_name;
                $sig['key'] = $key;
                continue;
            }

            $sig['book'] = $original_filename;

            // Set variables

            $file_hash = sha1_file($file['tmp_name']);
            $files_hash[$ext] = $file_hash;

            $is_image = starts_with($file['type'], 'image/');
            if ($is_image) {
                $files_hash['image'] = "${file_hash}.${ext}";
            }

            $filename = $file_hash . '.' . $ext;

            $total_input_files_array[$input_field_name][$key]['hash'] = $file_hash;


            // Check if this file is already stored.
            // Images covers, are a exception, and may be repeated (for example: different versions of same book)
            if (!$is_image && file_exists($upload_dir . $filename)) {
                throw new RuntimeException('ERROR: '.$original_filename.' -> File already exists.');
            }

            $total_files[] = $total_input_files_array[$input_field_name][$key];
        }


        // if exists .sig in html file input
        if (!empty($sig['input_field_name'])) {
            $total_input_files_array[$sig['input_field_name']][$sig['key']]['hash'] = $file_hash;
            $sig_original_filename = $total_input_files_array[$sig['input_field_name']][$sig['key']]['name'];
            $filename = $file_hash . '.sig';

            // Check if .sig is not alone
            if (empty($file_hash)) {
                throw new RuntimeException('ERROR: Signature file can\'t be alone on one field.');
            }

            // Check if this file is already stored
            if (file_exists($upload_dir . $filename)) {
                throw new RuntimeException("ERROR: The signature file \"$sig_original_filename\" for the book \"{$sig['book']}\" was already on the server.");
            }

            $sig_bool[$books_extension[$sig['input_field_name']]] = true;

            $total_files[] = $total_input_files_array[$sig['input_field_name']][$sig['key']];
        }
    }


    // Check if at least one book exists
    $book_number_array = [];

    
    foreach ($total_number_files_per_extension as $extension => $number_per_extension) {

        // if its a book
        foreach ($books_extension as $html_input_field => $book_extension) {
            if($extension == $book_extension) {
                $book_number_array[] = $number_per_extension;
            }
        }
    }
    

    if (array_sum($book_number_array) === 0) {
        throw new RuntimeException('ERROR: You must submit at least one book file.', 1);
    }


    foreach ($total_files as $file) {
        // You should name it uniquely.
        // DO NOT USE $file['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
        if (!move_uploaded_file(
            $file['tmp_name'],
            sprintf(
                $upload_dir . '%s.%s',
                $file['hash'],
                $file['ext']
            )
        )) {
            throw new RuntimeException('ERROR: '.$original_filename.' -> Failed to move uploaded file.');
        }
    }

    

    try {
        // Store on the database

        // require authentication data
        require './credentials.php';
        $table = 'books';

        // Create connection
        $conn = new mysqli($server, $mysql_user, $mysql_pass, $database);
        // Check connection
        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error, 1);
        }

        $sql = "INSERT INTO `${table}` (`book-title`, `book-author-name`, `book-genre`, `book-description`, `pdf-file-hash`, `has-pdf-sig`, `epub-file-hash`, `has-epub-sig`, `cover-filename`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);


        $stmt->bind_param('ssssssssss', $title, $author_name, $genre, $description, $pdf_hash, $has_pdf_sig, $epub_hash, $has_epub_sig, $cover_hash_filename, $created_at);


        $title = $_POST['book-title'];
        $author_name = $_POST['book-author-name'];
        $genre = $_POST['book-genre'];
        $description = $_POST['book-description'];
        $pdf_hash = $files_hash['pdf'] ?? 'null';
        $has_pdf_sig = $sig_bool['pdf'];
        $epub_hash = $files_hash['epub'] ?? 'null';
        $has_epub_sig = $sig_bool['epub'];
        $cover_hash_filename = $files_hash['image'] ?? 'null';
        $created_at = date('Y-m-d H:i:s');


        $stmt->execute();

        $stmt->close();

        // Final
        $book_id = $conn->insert_id;
        send_new_book_mail_warning($book_id, $title, $author_name, $pdf_hash, $epub_hash);

        echo 'Upload successful. Once approved for non-copyright infringements, the book will be available on the library.';
    } catch (\Exception $e) {
        echo $e->getMessage();
    } finally {
        $conn->close();
    }
} catch (\RuntimeException $e) {
    echo $e->getMessage();
} finally {
    unset($_SESSION['csrf_token']);
}
