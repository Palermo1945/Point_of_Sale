<?php
require_once('auth.php');
header('Location: home.php');
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Invoices - <?php echo $escape($customer['customer_name']); ?></title>
    <link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../dist/css/sb-admin-2.css">
    <link rel="stylesheet" href="../css/app-modern.css">
    <style>
        .customer-invoices { max-width: 1180px; margin: 0 auto; padding: 24px 0 40px; }
        .customer-invoices-header { display: flex; justify-content: space-between; align-items: end; gap: 20px; margin-bottom: 20px; }
        .customer-invoices-header h1 { margin: 0; color: #30494e; font-size: 27px; font-weight: 650; }
        .customer-invoices-header p { margin: 6px 0 0; color: #718084; }
        .invoice-search { width: min(100%, 320px); }
        .customer-summary { display: flex; flex-wrap: wrap; gap: 8px 24px; margin-bottom: 18px; padding: 14px 16px; border: 1px solid #dbe5e6; border-radius: 6px; background: rgba(255,255,255,.78); color: #586d71; }
        .customer-summary strong { color: #38565b; }
        .invoice-table-wrap { overflow-x: auto; border: 1px solid #dce6e7; border-radius: 6px; background: rgba(255,255,255,.86); }
        .invoice-table { min-width: 720px; margin: 0; }
        .invoice-table td { vertical-align: middle !important; }
        .invoice-link { white-space: nowrap; }
        .invoice-empty { padding: 24px; color: #718084; text-align: center; }
        @media(max-width:700px) {
            .customer-invoices { padding: 18px 0 28px; }
            .customer-invoices-header { align-items: stretch; flex-direction: column; }
            .invoice-search { width: 100%; }
        }
    </style>
</head>
<body>
    <?php include('navfixed.php'); ?>
    <main id="page-wrapper">
        <div class="customer-invoices">
            <header class="customer-invoices-header">
                <div>
                    <p class="cashier-kicker">CUSTOMER LEDGER</p>
                    <h1><?php echo $escape($customer['customer_name']); ?></h1>
                    <p>Choose a credit invoice to review payments or record a payment.</p>
                </div>
                <a class="btn btn-default" href="select_customer.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> All customers</a>
            </header>

            <div class="customer-summary">
                <span><strong>Contact:</strong> <?php echo $escape($customer['contact']); ?></span>
                <span><strong>Address:</strong> <?php echo $escape($customer['address']); ?></span>
                <span><strong>Membership:</strong> <?php echo $escape($customer['membership_number']); ?></span>
            </div>

            <div class="form-group invoice-search">
                <label for="invoice-search">Search invoices</label>
                <input class="form-control" id="invoice-search" type="search" placeholder="Invoice number or date" autocomplete="off">
            </div>

            <div class="invoice-table-wrap">
                <table class="table table-hover invoice-table">
                    <thead><tr><th>Invoice No.</th><th>Date</th><th>Due Date</th><th>Total</th><th>Balance</th><th>Ledger</th></tr></thead>
                    <tbody id="invoice-rows">
                        <?php foreach ($invoices as $invoice) { ?>
                            <tr class="invoice-row">
                                <td><?php echo $escape($invoice['invoice_number']); ?></td>
                                <td><?php echo $escape($invoice['date']); ?></td>
                                <td><?php echo $escape($invoice['due_date']); ?></td>
                                <td>&#8369;<?php echo number_format((float) $invoice['total_amount'], 2); ?></td>
                                <td>&#8369;<?php echo number_format((float) $invoice['current_balance'], 2); ?></td>
                                <td><a class="btn btn-primary btn-sm invoice-link" href="customer_ledger.php?cname=<?php echo urlencode($invoice['invoice_number']); ?>"><i class="fa fa-book" aria-hidden="true"></i> Open ledger</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php if (!$invoices) { ?><div class="invoice-empty">No credit invoices were found for this customer.</div><?php } else { ?>
                    <div id="invoice-empty" class="invoice-empty" style="display:none;">No invoices match your search.</div>
                <?php } ?>
            </div>
        </div>
    </main>
    <script>
        (function () {
            var search = document.getElementById('invoice-search');
            var rows = Array.prototype.slice.call(document.querySelectorAll('.invoice-row'));
            var empty = document.getElementById('invoice-empty');
            search.addEventListener('input', function () {
                var query = search.value.trim().toLowerCase();
                var visibleCount = 0;
                rows.forEach(function (row) {
                    var matches = row.textContent.toLowerCase().indexOf(query) !== -1;
                    row.style.display = matches ? '' : 'none';
                    if (matches) visibleCount++;
                });
                if (empty) empty.style.display = visibleCount === 0 ? 'block' : 'none';
            });
        })();
    </script>
</body>
</html>
