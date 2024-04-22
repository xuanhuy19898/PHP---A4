<?php
//I, Xuan Huy Pham, 000899551, certify that this material is my original work. 
//No other person's work has been used without suitable acknowledgment, and I have not made my work available to anyone else.

/**
 * @author Xuan Huy Pham
 * @version 20231211.00
 * @package COMP 10260 Assignment 4
 * 
 */
//set the content type to json
header('Content-Type: application/json');
//get the page parameters from the query string and validate it as int
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)));
$page = ($page !== false) ? $page : 1;
//set the limit of quotations at a time
$limit = 20;
$offset = ($page - 1) * $limit;

//info of database
$host = 'localhost';
$dbname = 'sa000899551';
$username = 'sa000899551';                         
$password = 'Sa_19980819';


//connect to database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

//SQL query used to get authors and their quote from 2 tables in database
$query = "SELECT quotes.quote_text, authors.author_name
          FROM quotes
          JOIN authors ON quotes.author_id = authors.author_id
          ORDER BY quotes.quote_id DESC
          LIMIT :per_page
          OFFSET :offset";
//prepare the sql 
$stmt = $pdo->prepare($query);
//bind parameters for limit and offset
$stmt->bindParam(':per_page', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();//execute the sql query
//fetch the rows as an associative array
$quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
//finish the connection to database
$pdo = null;
//create an array for outputs
$output = [];
//building the html cards
foreach ($quotations as $quote) {
    $output[] = '<div class="card mb-3 a4card w-100">
                    <div class="card-header">' . htmlspecialchars($quote['author_name'], ENT_QUOTES, 'UTF-8') . '</div>
                    <div class="card-body d-flex align-items-center">
                        <p class="card-text w-100">' . htmlspecialchars($quote['quote_text'], ENT_QUOTES, 'UTF-8') . '</p>
                    </div>
                </div>';
}
echo json_encode($output);
?>
