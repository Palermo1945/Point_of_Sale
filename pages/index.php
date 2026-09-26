<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>JGCML E-Device</title>
	<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="../images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="../vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="../vendor/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" href="../css/login.css">

	<link rel="shortcut icon" href="logoc.jpg">
	<!-- Bootstrap Core CSS -->
	<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- MetisMenu CSS -->
	<link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="../dist/css/sb-admin-2.css" rel="stylesheet">

	<!-- Custom Fonts -->
	<link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">


	<body>

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->



    </head>

    <body>
    	<?php
    	function createRandomPassword() {
    		$chars = "003232303232023232023456789";
    		srand((double)microtime()*1000000);
    		$i = 0;
    		$pass = '' ;
    		while ($i <= 7) {

    			$num = rand() % 33;

    			$tmp = substr($chars, $num, 1);

    			$pass = $pass . $tmp;

    			$i++;

    		}
    		return $pass;
    	}
    	$finalcode='RS-'.createRandomPassword();
    	?>

    	<div class="limiter">
    		
			<div class="container-login100" style="background-image: url('../images/bg-01.jpg');">
    			<div class="wrap-login100">
    				<div class="login100-form validate-form">
					<span class="login100-form-logo">
						<i class="zmdi zmdi-landscape"></i>
					</span>

    					<div class="panel-heading">
    						<h3 class="login100-form-title p-b-34 p-t-27"><b>JGCML E-Device </b></h3>
    					</div>
    					<ul class="nav nav-pills">
    						<li class="active"><a data-toggle="pill" href="#home">Admin</a></li>
    						<li><a data-toggle="pill" href="#menu2">Cashier</a></li>
    					</ul>
    					<div class="tab-content">
    						<!-- Department -->
    						<div id="home" class="tab-pane fade in active">
    							<br />
    							<form method="post" name="admin_form">
    								<div class="wrap-input100 validate-input">
    									<input type="text" class="input100" name="username" placeholder="Username">
										<span class="focus-input100" data-placeholder="&#xf207;"></span>
    								</div>
    								<div class="wrap-input100 validate-input">
    									<input type="password" class="input100" name="pass" placeholder="Password">
										<span class="focus-input100" data-placeholder="&#xf191;"></span>
    								</div>
    								<div class="container-login100-form-btn">
    									<button class="login100-form-btn" id = "btn-login" name = "btn-login">Log in</button>
    								</div>
    								<div class="form-group" id="alert-msg">
    								</div>

    							</form>
    						</div>
    						<div id="menu2" class="tab-pane fade">
    							<br />
    							<form method="post" name="cashier_form">
    								<div class="form-group">
    									<input type="text" class="form-control" name="cashier_username" placeholder="Username">
    								</div>
    								<div class="form-group">
    									<input type="password" class="form-control" name="cashier_pass" placeholder="Password">
    								</div>
    								<div class="form-group">
    									<button class="btn btn-block btn-success" id = "btn" name = "btn">Log in</button>
    								</div>
    								<div class="form-group" id="alert-msg1">
    								</div>
    							</form>
    						</div>
    					</div>
    				</div>
    			</div>
    		
	</div>

    	<!-- jQuery -->
    	<script src="../vendor/jquery/jquery.min.js"></script>

    	<!-- Bootstrap Core JavaScript -->
    	<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    	<!-- Metis Menu Plugin JavaScript -->
    	<script src="../vendor/metisMenu/metisMenu.min.js"></script>

    	<!-- Custom Theme JavaScript -->
    	<script src="../dist/js/sb-admin-2.js"></script>

    	<script type="text/javascript">
    		jQuery(function(){
    			$('form[name="admin_form"]').on('submit', function(donard){
    				donard.preventDefault();

    				var a = $(this).find('input[name="username"]').val();
    				var b = $(this).find('input[name="pass"]').val();

    				if (a === '' && b ===''){
    					$('#alert-msg').html('<div class="alert alert-danger">All fields are required!</div>');
    				}else{
    					$.ajax({
    						type: 'POST',
    						url: 'new_login.php',
    						data: {
    							username: a,
    							password: b
    						},
    						beforeSend:  function(){
    							$('#alert-msg').html('');
    						}
    					})
    					.done(function(donard){
    						if (donard == 0){
    							$('#alert-msg').html('<div class="alert alert-danger">Incorrect username or password!</div>');
    						}else{
    							$("#btn-login").html('<img src="loading.gif" /> &nbsp; Signing In ...');
    							setTimeout(' window.location.href = "home.php"; ',2000);
    						}
    					});
    				}
    			});

    			$('form[name="cashier_form"]').on('submit', function(donard){
    				donard.preventDefault();

    				var a = $(this).find('input[name="cashier_username"]').val();
    				var b = $(this).find('input[name="cashier_pass"]').val();

    				if (a === '' && b ===''){
    					$('#alert-msg1').html('<div class="alert alert-danger">All fields are required!</div>');
    				}else{
    					$.ajax({
    						type: 'POST',
    						url: 'cashier/new_login.php',
    						data: {
    							username: a,
    							password: b
    						},
    						beforeSend:  function(){
    							$('#alert-msg1').html('');
    						}
    					})
    					.done(function(donard){
    						if (donard == 0){
    							$('#alert-msg1').html('<div class="alert alert-danger">Incorrect username or password!</div>');
    						}else{
    							$("#btn").html('<img src="loading.gif" /> &nbsp; Signing In ...');
    							setTimeout(' window.location.href = "cashier/sales.php?id=cash&invoice=<?php echo $finalcode ?>"; ',2000);
    						}
    					});
    				}
    			});
    		});
    	</script>

    </body>

    </html>
