<?php
require_once('auth.php');

$invoice = isset($_GET['invoice']) ? trim($_GET['invoice']) : '';
if ($invoice === '') {
    http_response_code(400);
    exit('Missing receipt invoice number.');
}

$salesQuery = $db->prepare('SELECT * FROM sales WHERE invoice_number = :invoice LIMIT 1');
$salesQuery->execute(array(':invoice' => $invoice));
$sale = $salesQuery->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    http_response_code(404);
    exit('Receipt not found.');
}

$customerName = isset($sale['name']) ? $sale['name'] : '';
$customerAddress = 'Not provided';
$customerContact = 'Not provided';
$customerQuery = $db->prepare('SELECT address, contact FROM customer WHERE customer_name = :name LIMIT 1');
$customerQuery->execute(array(':name' => $customerName));
$customer = $customerQuery->fetch(PDO::FETCH_ASSOC);
if ($customer) {
    $customerAddress = $customer['address'];
    $customerContact = $customer['contact'];
}

$itemQuery = $db->prepare('SELECT product, name, dname, qty, price, discount, total_amount FROM sales_order WHERE invoice = :invoice ORDER BY transaction_id');
$itemQuery->execute(array(':invoice' => $invoice));
$items = $itemQuery->fetchAll(PDO::FETCH_ASSOC);
$invoiceTotal = 0;
foreach ($items as $item) {
    $invoiceTotal += (float) $item['total_amount'];
}

$paymentMethod = isset($sale['type']) ? strtolower($sale['type']) : '';
$cashReceived = $paymentMethod === 'cash' ? (float) $sale['cash'] : 0;
if ($paymentMethod === 'cash') {
    $amountPaid = min($invoiceTotal, max(0, $cashReceived));
    $amountReceived = max(0, $cashReceived);
} else {
    $paymentsQuery = $db->prepare('SELECT COALESCE(SUM(amount), 0) FROM collection WHERE name = :invoice');
    $paymentsQuery->execute(array(':invoice' => $invoice));
    $amountPaid = min($invoiceTotal, max(0, (float) $paymentsQuery->fetchColumn()));
    $amountReceived = $amountPaid;
}
$balance = max(0, $invoiceTotal - $amountPaid);
$change = max(0, $cashReceived - $invoiceTotal);
$paymentStatus = $balance < 0.005 ? 'Paid in Full' : ($amountPaid > 0 ? 'Partial Payment' : 'Unpaid');
$receiptDate = isset($sale['date']) ? $sale['date'] : '';
$cashierName = isset($sale['cashier']) ? $sale['cashier'] : '';

$escape = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$formatAmount = function ($value) {
    return '&#8369;' . number_format((float) $value, 2);
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Receipt - <?php echo $escape($invoice); ?></title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --receipt-ink: #26343b;
            --receipt-muted: #65757d;
            --receipt-line: #d8e0e4;
            --receipt-glass: rgba(255, 255, 255, 0.88);
            --receipt-accent: #536b75;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 34px 18px;
            color: var(--receipt-ink);
            font-family: "Segoe UI", Arial, sans-serif;
            background:
                linear-gradient(135deg, transparent 0 43%, rgba(255,255,255,.38) 43.1% 43.5%, transparent 43.6%),
                linear-gradient(35deg, transparent 0 69%, rgba(90,110,120,.1) 69.1% 69.5%, transparent 69.6%),
                linear-gradient(145deg, #e7ecef, #cbd4d9 52%, #e1e7ea);
        }

        .receipt-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 0 auto 18px;
        }

        .receipt-actions .btn {
            border: 1px solid #536b75;
            border-radius: 5px;
            color: #fff;
            background: #536b75;
        }

        .receipt {
            width: min(100%, 760px);
            margin: 0 auto;
            padding: 38px 44px;
            border: 1px solid rgba(255,255,255,.92);
            border-radius: 8px;
            background: var(--receipt-glass);
            box-shadow: 0 22px 65px rgba(39, 53, 61, .2), inset 0 1px rgba(255,255,255,.95);
            -webkit-backdrop-filter: blur(16px);
            backdrop-filter: blur(16px);
        }

        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 22px;
            padding-bottom: 24px;
            border-bottom: 2px solid var(--receipt-ink);
        }

        .business-name {
            margin: 0 0 7px;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .business-details {
            color: var(--receipt-muted);
            font-size: 12px;
            line-height: 1.65;
        }

        .receipt-title {
            min-width: 190px;
            text-align: right;
        }

        .receipt-title h1 {
            margin: 0 0 14px;
            color: var(--receipt-accent);
            font-size: 23px;
            font-weight: 700;
            letter-spacing: .03em;
        }

        .receipt-meta {
            margin: 0;
            font-size: 13px;
            line-height: 1.8;
        }

        .receipt-meta strong { color: var(--receipt-muted); }

        .receipt-section { margin-top: 24px; }

        .section-title {
            margin: 0 0 11px;
            color: var(--receipt-accent);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .customer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 24px;
            padding: 16px 18px;
            border: 1px solid var(--receipt-line);
            border-radius: 5px;
            background: rgba(241, 245, 247, .72);
        }

        .customer-field label,
        .summary-table th {
            display: block;
            margin: 0 0 4px;
            color: var(--receipt-muted);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .customer-field div { font-size: 14px; font-weight: 600; overflow-wrap: anywhere; }

        .item-table,
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-table th {
            padding: 10px 8px;
            border-bottom: 1px solid var(--receipt-ink);
            color: var(--receipt-muted);
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }

        .item-table td {
            padding: 10px 8px;
            border-bottom: 1px solid var(--receipt-line);
            font-size: 12px;
            vertical-align: top;
        }

        .item-table .numeric { text-align: right; white-space: nowrap; }
        .item-description { color: var(--receipt-muted); font-size: 11px; }

        .summary-table th,
        .summary-table td {
            padding: 9px 12px;
            border-bottom: 1px solid var(--receipt-line);
            text-align: left;
        }

        .summary-table td { font-size: 14px; text-align: right; white-space: nowrap; }
        .summary-table tr:last-child th,
        .summary-table tr:last-child td { border-bottom: 0; }
        .summary-table .balance-row th,
        .summary-table .balance-row td { color: #9a4a42; font-size: 16px; font-weight: 700; }

        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 9px 20px;
            color: var(--receipt-ink);
            font-size: 13px;
        }

        .payment-methods span { white-space: nowrap; }
        .payment-meta { margin-top: 16px; }

        .amount-received {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 18px;
            padding: 15px 18px;
            border: 1px solid #c5d1d6;
            border-left: 4px solid var(--receipt-accent);
            border-radius: 4px;
            background: linear-gradient(110deg, rgba(227,235,239,.92), rgba(255,255,255,.85));
        }

        .amount-received strong:first-child {
            color: var(--receipt-muted);
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .amount-value { font-size: 22px; font-weight: 700; }
        .change-note { margin-top: 7px; color: var(--receipt-muted); font-size: 12px; text-align: right; }

        .receipt-footer { margin-top: 34px; }
        .status-line { font-size: 13px; font-weight: 600; }
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 38px;
        }

        .signature-line {
            min-height: 28px;
            padding-top: 7px;
            border-top: 1px solid #73828a;
            color: var(--receipt-muted);
            font-size: 11px;
        }

        .thank-you {
            margin: 32px 0 0;
            color: var(--receipt-accent);
            font-size: 16px;
            font-weight: 700;
            text-align: center;
        }

        @media (max-width: 600px) {
            body { padding: 14px 8px; }
            .receipt { padding: 24px 18px; }
            .receipt-header { display: block; }
            .receipt-title { margin-top: 20px; text-align: left; }
            .customer-grid { grid-template-columns: 1fr; }
            .item-table th, .item-table td { padding: 8px 4px; }
            .item-description { display: block; }
            .signature-grid { gap: 18px; }
        }

        @media print {
            @page { margin: 12mm; }
            body { min-height: 0; padding: 0; background: #fff; }
            .receipt-actions { display: none !important; }
            .receipt {
                width: 100%;
                margin: 0;
                padding: 0;
                border: 0;
                border-radius: 0;
                background: #fff;
                box-shadow: none;
                -webkit-backdrop-filter: none;
                backdrop-filter: none;
            }
            .customer-grid, .amount-received { background: #fff; }
            * { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="receipt-actions">
        <button class="btn" type="button" onclick="window.print()"><i class="fa fa-print" aria-hidden="true"></i> Print Receipt</button>
        <a class="btn" href="home.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
    </div>

    <main class="receipt">
        <header class="receipt-header">
            <div>
                <h2 class="business-name">JGCML Device</h2>
                <div class="business-details">
                    Street Address: Rizal Proper Silay City<br>
                    Brgy: Rizal<br>
                    Contact No: 09317901184<br>
                    Email: JGCML@gmail.com
                </div>
            </div>
            <div class="receipt-title">
                <h1>Payment Receipt</h1>
                <p class="receipt-meta"><strong>Receipt No.:</strong> <?php echo $escape($invoice); ?><br>
                <strong>Date:</strong> <?php echo $escape($receiptDate); ?></p>
            </div>
        </header>

        <section class="receipt-section">
            <h2 class="section-title">Received From</h2>
            <div class="customer-grid">
                <div class="customer-field"><label>Customer / Client</label><div><?php echo $escape($customerName); ?></div></div>
                <div class="customer-field"><label>Invoice No.</label><div><?php echo $escape($invoice); ?></div></div>
                <div class="customer-field"><label>Address</label><div><?php echo $escape($customerAddress); ?></div></div>
                <div class="customer-field"><label>Contact No.</label><div><?php echo $escape($customerContact); ?></div></div>
            </div>
        </section>

        <section class="receipt-section">
            <h2 class="section-title">Description</h2>
            <table class="item-table">
                <thead><tr><th>Product</th><th>Qty</th><th class="numeric">Unit Price</th><th class="numeric">Amount</th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item) { ?>
                        <tr>
                            <td><?php echo $escape($item['product']); ?> <span class="item-description">/ <?php echo $escape($item['name']); ?><?php if (!empty($item['dname'])) { ?>, <?php echo $escape($item['dname']); ?><?php } ?></span></td>
                            <td><?php echo $escape($item['qty']); ?></td>
                            <td class="numeric"><?php echo $formatAmount($item['price']); ?></td>
                            <td class="numeric"><?php echo $formatAmount($item['total_amount']); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (!$items) { ?>
                        <tr><td colspan="4">No item details were found for this invoice.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <section class="receipt-section">
            <h2 class="section-title">Payment Details</h2>
            <table class="summary-table">
                <tbody>
                    <tr><th>Description</th><td>Amount</td></tr>
                    <tr><th>Invoice Total</th><td><?php echo $formatAmount($invoiceTotal); ?></td></tr>
                    <tr><th>Amount Paid</th><td><?php echo $formatAmount($amountPaid); ?></td></tr>
                    <tr class="balance-row"><th>Balance</th><td><?php echo $formatAmount($balance); ?></td></tr>
                </tbody>
            </table>
            <div class="payment-meta">
                <p class="section-title">Payment Method</p>
                <div class="payment-methods">
                    <span><?php echo $paymentMethod === 'cash' ? '&#9745;' : '&#9744;'; ?> Cash</span>
                    <span>&#9744; Bank Transfer</span>
                    <span>&#9744; GCash</span>
                    <span><?php echo $paymentMethod === 'credit' ? '&#9745;' : '&#9744;'; ?> Credit</span>
                    <span>&#9744; Other: __________</span>
                </div>
            </div>
            <div class="amount-received">
                <strong>Amount Received</strong>
                <span class="amount-value"><?php echo $formatAmount($amountReceived); ?></span>
            </div>
            <?php if ($paymentMethod === 'cash' && $change > 0) { ?>
                <p class="change-note">Change returned: <?php echo $formatAmount($change); ?></p>
            <?php } ?>
        </section>

        <footer class="receipt-footer">
            <p class="status-line">Payment Status: <?php echo $paymentStatus === 'Paid in Full' ? '&#9745;' : '&#9744;'; ?> Paid in Full &nbsp;&nbsp; <?php echo $paymentStatus === 'Partial Payment' ? '&#9745;' : '&#9744;'; ?> Partial Payment &nbsp;&nbsp; <?php echo $paymentStatus === 'Unpaid' ? '&#9745;' : '&#9744;'; ?> Unpaid</p>
            <div class="signature-grid">
                <div class="signature-line">Received by: <?php echo $escape($cashierName); ?></div>
                <div class="signature-line">Signature</div>
            </div>
            <p class="thank-you">Thank you for your payment!</p>
        </footer>
    </main>
</body>
</html>


