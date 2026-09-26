<?php
require_once('auth.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>CURE GROCERY</title>
	
	<link rel="shortcut icon" href="logo.jpg">
	<!-- Bootstrap Core CSS -->
	<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- MetisMenu CSS -->
	<link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="dist/css/sb-admin-2.css" rel="stylesheet">
	<link href="css/cashier-pos.css" rel="stylesheet">

	<!-- Morris Charts CSS -->
	<link href="vendor/morrisjs/morris.css" rel="stylesheet">

	<!-- Custom Fonts -->
	<link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">


	<link href="css/bootstrap-datepicker.min.css" rel="stylesheet">

	<link href="js/datepicker.js" rel="stylesheet">

	<link href="js/bootstrap-datepicker.min.js" rel="stylesheet">
	
	<link href="src/facebox.css" media="screen" rel="stylesheet" type="text/css" />
	<script src="lib/jquery.js" type="text/javascript"></script>
	<script src="src/facebox.js" type="text/javascript"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('a[rel*=facebox]').facebox({
				loadingImage : 'src/loading.gif',
				closeImage   : 'src/closelabel.png'
			})
		})
	</script>


</head>

<body>

	<div id="wrapper">
		
		<?php include('navfixed.php');?>


		<div id="page-wrapper" class="cashier-home">
			<div class="cashier-home-header">
				<p class="cashier-kicker">JGCML GROCERY / CASHIER</p>
				<h1>Welcome, <?php echo htmlspecialchars($session_cashier_name, ENT_QUOTES, 'UTF-8'); ?></h1>
				<p>Ready to start a sale?</p>
			</div>
			<section class="cashier-home-panel">
				<h2>Choose payment type</h2>
				<p>Start a new transaction by selecting how the customer will pay.</p>
				<div class="cashier-payment-options">
					<a href="sales.php?id=cash&amp;invoice=<?php echo urlencode($finalcode); ?>"><i class="fa fa-money" aria-hidden="true"></i> Cash sale</a>
					<a href="sales.php?id=credit&amp;invoice=<?php echo urlencode($finalcode); ?>"><i class="fa fa-clock-o" aria-hidden="true"></i> Credit sale</a>
				</div>
			</section>
		</div>

		<!-- /.row -->
	</div>
	<!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->


<!-- jQuery -->
<script src="vendor/jquery/jquery.min.js"></script>


<!-- Bootstrap Core JavaScript -->
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="vendor/metisMenu/metisMenu.min.js"></script>

<!-- Morris Charts JavaScript -->
<script src="vendor/raphael/raphael.min.js"></script>
<script src="vendor/morrisjs/morris.min.js"></script>
<script src="data/morris-data.js"></script>

<!-- Custom Theme JavaScript -->
<script src="dist/js/sb-admin-2.js"></script>
<script>
	$('.carousel').carousel({
        interval: 3000 //changes the speed
    })
</script>

</body>

</html>
