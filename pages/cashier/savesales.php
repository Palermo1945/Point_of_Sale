<?php
session_start();
include('connect.php');
require_once __DIR__ . '/../customer_utils.php';
$a = $_POST['invoice'];
$b = $_POST['cashier'];
$c = $_POST['date'];
$d = $_POST['ptype'];
$e = $_POST['amount'];
$pamount = $_POST['p_amount'];
$cname = normalizeCustomerName(isset($_POST['cname']) ? $_POST['cname'] : '');
$vat=$pamount*.12;

$date = date('m-d-Y');

$dmonth = date('F');
$dyear = date('Y');

if ($cname === '') {
	header('Location: sales.php?id=' . urlencode($d) . '&invoice=' . urlencode($a) . '&error=customer_required');
	exit();
}

if($d=='credit') {
	$f = isset($_POST['due']) ? $_POST['due'] : '';
	if ($f === '') {
		header('Location: sales.php?id=credit&invoice=' . urlencode($a) . '&error=due_date_required');
		exit();
	}
	$cname = ensureCustomerRecord($db, $cname);
	$sql = "INSERT INTO sales (invoice_number,cashier,date,type,total_amount,due_date,name,month,year,balance,p_amount,vat) VALUES (:a,:b,:c,:d,:e,:f,:g,:h,:i,:k,:j,:l)";
	$q = $db->prepare($sql);
	$q->execute(array(':a'=>$a,':b'=>$b,':c'=>$c,':d'=>$d,':e'=>$e,':f'=>$f,':g'=>$cname,':h'=>$dmonth,':i'=>$dyear,':k'=>$e,':j'=>$pamount,':l'=>$vat));
	header("location: preview.php?invoice=$a");
	exit();
}
if($d=='cash') {
	$cashReceived = isset($_POST['cash']) ? filter_var($_POST['cash'], FILTER_VALIDATE_FLOAT) : false;
	$totalQuery = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM sales_order WHERE invoice = :invoice");
	$totalQuery->execute(array(':invoice' => $a));
	$amountDue = (float) $totalQuery->fetchColumn();
	if ($cashReceived === false || $cashReceived < 0 || $cashReceived < $amountDue) {
		header('Location: sales.php?id=cash&invoice=' . urlencode($a) . '&error=insufficient_cash');
		exit();
	}
	$f = $cashReceived;
	$cname = ensureCustomerRecord($db, $cname);
	$sql = "INSERT INTO sales (invoice_number,cashier,date,type,amount,cash,name,month,year,p_amount,vat) VALUES (:a,:b,:c,:d,:e,:f,:g,:h,:i,:k,:j)";
	$q = $db->prepare($sql);	$q->execute(array(':a'=>$a,':b'=>$b,':c'=>$c,':d'=>$d,':e'=>$e,':f'=>$f,':g'=>$cname,':h'=>$dmonth,':i'=>$dyear,':k'=>$pamount,':j'=>$vat));
	header("location: preview.php?invoice=$a");
	exit();
}
// query

?>
