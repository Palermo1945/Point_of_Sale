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

  <!-- Custom Fonts -->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
      </head>

      <body>

        <?php include('navfixed.php');?>

        <div id="page-wrapper" class="cashier-page">
          <div class="row">
            <div class="col-lg-12">
              <header class="cashier-page-header">
                <div>
                  <p class="cashier-kicker">POINT OF SALE</p>
                  <h1>New sale</h1>
                </div>
                <div class="cashier-invoice"><span>Invoice</span><strong><?php echo htmlspecialchars($_GET['invoice'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
              </header>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'insufficient_cash') { ?>
              <div class="col-lg-12">
                <div class="alert alert-danger" role="alert">Cash received must be equal to or greater than the invoice total. Update the amount below and submit again.</div>
              </div>
            <?php } elseif (isset($_GET['error']) && $_GET['error'] === 'customer_required') { ?>
              <div class="col-lg-12">
                <div class="alert alert-danger" role="alert">Enter a customer name before completing the sale.</div>
              </div>
            <?php } elseif (isset($_GET['error']) && $_GET['error'] === 'due_date_required') { ?>
              <div class="col-lg-12">
                <div class="alert alert-danger" role="alert">Select a due date before completing a credit sale.</div>
              </div>
            <?php } ?>

            <div id="maintable" class="cashier-workspace">
            <form action="incoming.php" method="post" class="cashier-entry-form">
              <input type="hidden" name="pt" value="<?php echo htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8'); ?>" />
              <input type="hidden" name="invoice" value="<?php echo htmlspecialchars($_GET['invoice'], ENT_QUOTES, 'UTF-8'); ?>" />
              <div class="cashier-field cashier-product-field">
                <label for="cashier-product">Select a product</label>
              <select id="cashier-product" name="product" class="chzn-select">
                <option></option>
                <?php
                include('connect.php');
                $result = $db->prepare("SELECT * FROM products");
                $result->execute();
                for($i=0; $row = $result->fetch(); $i++){
                  ?>
                  <option value="<?php echo $row['product_code'];?>" 
                    <?php
                    if($row['qty_left'] == 0)
                    {
                      echo'disabled';
                    }
                    ?>
                    >
                    <?php echo $row['product_code']; ?>
                    - <?php echo $row['product_name']; ?>
                    - <?php echo $row['description_name']; ?>
                    - <?php echo $row['qty_left']; ?>

                  </option>
                  <?php
                }
                ?>
              </select>
              </div>
              <div class="cashier-field">
                <label for="cashier-quantity">Quantity</label>
                <input id="cashier-quantity" type="number" name="qty" value="1" min="1" class="form-control" autocomplete="off" />
              </div>
              <div class="cashier-field">
                <label for="cashier-discount">Discount</label>
                <input id="cashier-discount" type="number" name="discount" value="0" min="0" step="0.01" class="form-control" autocomplete="off" />
              </div>
              <div class="cashier-field">
                <label for="cashier-vat">VAT rate</label>
                <input id="cashier-vat" type="number" name="vat" value="0.12" min="0" step="0.01" class="form-control" autocomplete="off" />
              </div>
              <button type="submit" class="btn btn-primary cashier-add-button"><i class="fa fa-plus" aria-hidden="true"></i> Add product</button>
            </form>
            <section class="cashier-cart-panel" aria-label="Current sale items">
              <div class="cashier-cart-heading">
                <div><p class="cashier-kicker">CURRENT SALE</p><h2>Cart</h2></div>
                <span class="cashier-cart-count">Invoice items</span>
              </div>
              <div class="cashier-table-scroll">
            <table width="100%" class="table table-striped table-bordered table-hover cashier-cart-table" id="dataTables-example">
              <thead>
                <tr>
                  <th> Product Code </th>
                  <th> Brand Name </th>
                  <th> Description Name </th>
                  <th> Category </th>
                  <th> Quantity </th>
                  <th> Price </th>
                  <th> Discount </th>
                  <th> VAT </th>
                  <th> Amount </th>
                  <th> Total Amount </th>
                  <th> Delete </th>
                </tr>
              </thead>
              <tbody>

                <?php
                $id=$_GET['invoice'];
                include('connect.php');
                $result = $db->prepare("SELECT * FROM sales_order WHERE invoice= :userid");
                $result->bindParam(':userid', $id);
                $result->execute();
                for($i=0; $row = $result->fetch(); $i++){
                  ?>
                  <tr class="record">
                    <td><?php echo $row['product']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['dname']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['qty']; ?></td>
                    <td>
                      <?php
                      $ppp=$row['price'];
                      echo formatMoney($ppp, true);
                      ?>
                    </td>
                    <td>
                      <?php
                      $ddd=$row['discount'];
                      echo formatMoney($ddd, true);
                      ?>
                    </td>
                    <td>
                      <?php
                      $fff=$row['vat'];
                      echo formatMoney($fff, true);
                      ?>
                    </td>
                    <td>
                      <?php
                      $ccc=$row['amount'];
                      echo formatMoney($ccc, true);
                      ?>
                    </td>

                    <td>
                      <?php
                      $dfdf=$row['total_amount'];
                      echo formatMoney($dfdf, true);
                      ?>
                    </td>
                    
                    <td><a href="delete.php?id=<?php echo $row['transaction_id']; ?>&invoice=<?php echo $_GET['invoice']; ?>&dle=<?php echo $_GET['id']; ?>&qty=<?php echo $row['qty'];?>&code=<?php echo $row['product'];?>"> Delete</a></td>
                  </tr>
                  <?php
                }
                ?>
                <tr>
                  <td colspan="9"><strong>Total due</strong></td>
                  <td colspan="2"><strong class="cashier-total-value">
                    <?php
                    function formatMoney($number, $fractional=false) {
                      if ($fractional) {
                        $number = sprintf('%.2f', $number);
                      }
                      while (true) {
                        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
                        if ($replaced != $number) {
                          $number = $replaced;
                        } else {
                          break;
                        }
                      }
                      return $number;
                    }
                    $sdsd=$_GET['invoice'];
                    $resultas = $db->prepare("SELECT sum(total_amount) FROM sales_order WHERE invoice= :a");
                    $resultas->bindParam(':a', $sdsd);
                    $resultas->execute();
                    for($i=0; $rowas = $resultas->fetch(); $i++){
                      $fgfg=$rowas['sum(total_amount)'];
                      echo formatMoney($fgfg, true);
                    }
                    $baseAmountQuery = $db->prepare("SELECT COALESCE(SUM(amount), 0) FROM sales_order WHERE invoice = :invoice");
                    $baseAmountQuery->execute(array(':invoice' => $sdsd));
                    $baseAmount = (float) $baseAmountQuery->fetchColumn();
                    ?>
                  </strong></td>
                </tr>

              </tbody>
            </table><br>
              </div>
              <div class="cashier-cart-footer">
                <div><span>Total due</span><strong>&#8369;<?php echo isset($fgfg) ? number_format((float) $fgfg, 2) : '0.00'; ?></strong></div>
                <?php if (isset($fgfg) && (float) $fgfg > 0) { ?>
                  <?php
                  $customerOptions = $db->query("SELECT customer_name FROM customer ORDER BY customer_name");
                  $paymentType = isset($_GET['id']) && $_GET['id'] === 'credit' ? 'credit' : 'cash';
                  ?>
                  <form class="cashier-payment-form" action="savesales.php" method="post">
                    <input type="hidden" name="invoice" value="<?php echo htmlspecialchars($_GET['invoice'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="cashier" value="<?php echo htmlspecialchars($session_cashier_name, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="date" value="<?php echo date('m/d/Y'); ?>">
                    <input type="hidden" name="ptype" value="<?php echo $paymentType; ?>">
                    <input type="hidden" name="amount" value="<?php echo number_format((float) $fgfg, 2, '.', ''); ?>">
                    <input type="hidden" name="p_amount" value="<?php echo number_format($baseAmount, 2, '.', ''); ?>">
                    <div class="cashier-payment-fields">
                      <div class="cashier-field">
                        <label for="cashier-customer">Customer name</label>
                        <input id="cashier-customer" class="form-control" type="text" name="cname" list="cashier-customer-options" placeholder="Enter or choose a customer" autocomplete="off" required>
                        <datalist id="cashier-customer-options">
                          <?php while ($customerOption = $customerOptions->fetch(PDO::FETCH_ASSOC)) { ?>
                            <option value="<?php echo htmlspecialchars($customerOption['customer_name'], ENT_QUOTES, 'UTF-8'); ?>"></option>
                          <?php } ?>
                        </datalist>
                      </div>
                      <?php if ($paymentType === 'cash') { ?>
                        <div class="cashier-field">
                          <label for="cashier-cash">Cash received</label>
                          <input id="cashier-cash" class="form-control" type="number" name="cash" min="<?php echo number_format((float) $fgfg, 2, '.', ''); ?>" step="0.01" placeholder="At least <?php echo number_format((float) $fgfg, 2); ?>" required>
                        </div>
                      <?php } else { ?>
                        <div class="cashier-field">
                          <label for="cashier-due-date">Payment due date</label>
                          <input id="cashier-due-date" class="form-control" type="date" name="due" required>
                        </div>
                      <?php } ?>
                      <button type="submit" class="btn btn-primary cashier-checkout-button"><i class="fa fa-check" aria-hidden="true"></i> Complete sale</button>
                    </div>
                  </form>
                <?php } else { ?>
                  <span class="cashier-checkout-button cashier-disabled-action"><i class="fa fa-arrow-right" aria-hidden="true"></i> Add an item to continue</span>
                <?php } ?>
              </div>
            </section>

            <div class="clearfix"></div>
          </div>

        </div>
      </div>
      <!-- /#page-wrapper -->



      <!-- jQuery -->
      <script src="vendor/jquery/jquery.min.js"></script>

      <!-- Bootstrap Core JavaScript -->
      <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

      <!-- Metis Menu Plugin JavaScript -->
      <script src="vendor/metisMenu/metisMenu.min.js"></script>

      <!-- Custom Theme JavaScript -->
      <script src="dist/js/sb-admin-2.js"></script>

      <link href="vendor/chosen.min.css" rel="stylesheet" media="screen">
      <script src="vendor/chosen.jquery.min.js"></script>
      <script>
        $(function() {
          $(".chzn-select").chosen();

        });
      </script>

    </body>

    </html>
