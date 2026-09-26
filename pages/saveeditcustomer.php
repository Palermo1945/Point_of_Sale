<?php
// configuration
include('connect.php');
require_once('customer_utils.php');

// new data
$id = $_POST['memi'];
$a = normalizeCustomerName(isset($_POST['name']) ? $_POST['name'] : '');
$b = $_POST['address'];
$c = $_POST['contact'];
$d = $_POST['memno'];

$duplicateQuery = $db->prepare('SELECT customer_id FROM customer WHERE LOWER(TRIM(customer_name)) = :name AND customer_id <> :id LIMIT 1');
$duplicateQuery->execute(array(':name' => $a, ':id' => $id));
if ($a === '' || $duplicateQuery->fetchColumn() !== false) {
	header('Location: customer.php?error=name_duplicate');
	exit();
}

// query
$sql = "UPDATE customer 
        SET customer_name=?, address=?, contact=?, membership_number=?
		WHERE customer_id=?";
$q = $db->prepare($sql);
$q->execute(array($a,$b,$c,$d,$id));
header("location: customer.php");

?>