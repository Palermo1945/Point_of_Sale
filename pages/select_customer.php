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
    <title>Customer Ledger</title>
    <link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../dist/css/sb-admin-2.css">
    <link rel="stylesheet" href="../css/app-modern.css">
    <style>
        .ledger-directory { max-width: 1180px; margin: 0 auto; padding: 24px 0 40px; }
        .ledger-directory-header { display: flex; justify-content: space-between; align-items: end; gap: 18px; margin-bottom: 20px; }
        .ledger-directory-header h1 { margin: 0; color: #30494e; font-size: 28px; font-weight: 650; }
        .ledger-directory-header p { margin: 6px 0 0; color: #728286; }
        .ledger-search { position: relative; width: min(100%, 340px); }
        .ledger-search .fa-search { position: absolute; top: 13px; left: 13px; color: #819194; }
        .ledger-search input { height: 42px; padding-left: 37px; }
        .ledger-directory-table { overflow: hidden; border: 1px solid #dce6e7; border-radius: 6px; background: rgba(255,255,255,.86); }
        .ledger-directory-table table { margin: 0; }
        .ledger-directory-table td { vertical-align: middle !important; }
        .ledger-customer-name { color: #314f54; font-weight: 650; }
        .ledger-open-link { white-space: nowrap; }
        .ledger-empty { display: none; padding: 26px; color: #6f8084; text-align: center; }
        @media (max-width: 700px) {
            .ledger-directory { padding: 18px 0 28px; }
            .ledger-directory-header { align-items: stretch; flex-direction: column; }
            .ledger-search { width: 100%; }
            .ledger-directory-table { overflow-x: auto; }
            .ledger-directory-table table { min-width: 680px; }
        }
    </style>
</head>
<body>
    <?php include('navfixed.php'); ?>
    <main id="page-wrapper">
        <div class="ledger-directory">
            <header class="ledger-directory-header">
                <div>
                    <p class="cashier-kicker">CUSTOMER ACCOUNTS</p>
                    <h1>Customer Ledger</h1>
                    <p>Select a customer to view their credit invoices and payment history.</p>
                </div>
                <label class="ledger-search">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input class="form-control" id="customer-search" type="search" placeholder="Search name, contact, membership" autocomplete="off">
                </label>
            </header>

            <div class="ledger-directory-table">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Customer</th><th>Contact</th><th>Membership No.</th><th>Address</th><th>Ledger</th></tr>
                    </thead>
                    <tbody id="customer-rows">
                        <?php foreach ($customers as $customer) { ?>
                            <tr class="customer-row">
                                <td class="ledger-customer-name"><?php echo $escape($customer['customer_name']); ?></td>
                                <td><?php echo $escape($customer['contact']); ?></td>
                                <td><?php echo $escape($customer['membership_number']); ?></td>
                                <td><?php echo $escape($customer['address']); ?></td>
                                <td><a class="btn btn-primary btn-sm ledger-open-link" href="customer_invoices.php?customer_id=<?php echo urlencode($customer['customer_id']); ?>"><i class="fa fa-book" aria-hidden="true"></i> View ledger</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div id="customer-empty" class="ledger-empty"><?php echo $customers ? 'No customers match your search.' : 'No customers have been added yet.'; ?></div>
            </div>
        </div>
    </main>
    <script>
        (function () {
            var search = document.getElementById('customer-search');
            var rows = Array.prototype.slice.call(document.querySelectorAll('.customer-row'));
            var empty = document.getElementById('customer-empty');
            search.addEventListener('input', function () {
                var query = search.value.trim().toLowerCase();
                var visibleCount = 0;
                rows.forEach(function (row) {
                    var matches = row.textContent.toLowerCase().indexOf(query) !== -1;
                    row.style.display = matches ? '' : 'none';
                    if (matches) visibleCount++;
                });
                empty.style.display = visibleCount === 0 ? 'block' : 'none';
            });
        })();
    </script>
</body>
</html>
