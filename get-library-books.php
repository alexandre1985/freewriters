<?php
header('Content-Type: application/json;charset=utf-8');

try {
    $json = '';

    // require authentication data
    require './credentials.php';
    $table = 'books';

    // Create connection
    $conn = new mysqli($server, $mysql_user, $mysql_pass, $database);
    // Check connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error, 1);
    }

    // find out how many rows are in the table

    //$result = mysql_query("SELECT COUNT(*) FROM ${table}");
    //$num_rows = mysql_result($result, 0, 0);
    $stmt = $conn->prepare("SELECT COUNT(*) FROM ${table} WHERE active=1");
    $stmt->execute();
    $result_array = $stmt->get_result()->fetch_assoc();
    $numrows_approved_works = $result_array['COUNT(*)'];

    // number of rows to show per page
    $rowsperpage = 10;
    // find out total pages
    $totalpages = ceil($numrows_approved_works / $rowsperpage);


    $sql = "SELECT * FROM ${table} WHERE `active` = 1 ORDER BY `created_at` DESC LIMIT ?, ${rowsperpage}";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param('i', $offset);


    // get the current page or set a default
    if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
        // cast var as int
        $currentpage = (int) $_GET['currentpage'];
    } else {
        // default page num
        $currentpage = 1;
    } // end if

    // if current page is greater than total pages...
    if ($currentpage > $totalpages) {
        // set current page to last page
        $currentpage = $totalpages;
    } // end if
    // if current page is less than first page...
    if ($currentpage < 1) {
        // set current page to first page
        $currentpage = 1;
    } // end if

    // the offset of the list, based on current page
    $offset = ($currentpage - 1) * $rowsperpage;


    $stmt->execute();

    //fetching rows
    $result = $stmt->get_result();
    //if ($result->num_rows === 0) {
    //    throw new Exception('No rows', 1);
    //}
    $books_array_output = [];
    while ($row = $result->fetch_assoc()) {
        $books_array_output[] = $row;
    }

    $stmt->close();
    

    // create json
    $json = ['error' => false, 'total_pages' => $totalpages, 'books' => $books_array_output, 'numrows_approved_works' => $numrows_approved_works];
} catch (Exception $e) {
    $json = ['error' => true, 'message' => $e->getMessage()];
} finally {
    $conn->close();
    echo json_encode($json, JSON_PRETTY_PRINT);
}
