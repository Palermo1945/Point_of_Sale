<?php
session_start();
include('connect.php');
require_once('customer_utils.php');

$firstName = normalizeCustomerName(isset($_POST['fname']) ? $_POST['fname'] : '');
$middleName = normalizeCustomerName(isset($_POST['mname']) ? $_POST['mname'] : '');
$lastName = normalizeCustomerName(isset($_POST['lname']) ? $_POST['lname'] : '');
$customerName = normalizeCustomerName(trim($firstName . ' ' . $middleName . ' ' . $lastName));

if ($firstName === '' || $lastName === '') {
	header('Location: customer.php?error=name_required');
	exit();
}

ensureCustomerRecord($db, $customerName, array(
	'first_name' => $firstName,
	'middle_name' => $middleName,
	'last_name' => $lastName,
	'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
	'contact' => isset($_POST['contact']) ? trim($_POST['contact']) : '',
	'membership_number' => isset($_POST['memno']) ? trim($_POST['memno']) : ''
));

header("location: customer.php");


?>