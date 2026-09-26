<?php
$isCashierNavigation = isset($navigationRole) && $navigationRole === 'cashier';
$navigationName = $isCashierNavigation ? $session_cashier_name : $session_admin_name;

if ($isCashierNavigation) {
    function createRandomPassword() {
        $chars = "003232303232023232023456789";
        srand((double) microtime() * 1000000);
        $password = '';
        for ($index = 0; $index <= 7; $index++) {
            $password .= substr($chars, rand() % 33, 1);
        }
        return $password;
    }
    $finalcode = 'RS-' . createRandomPassword();
}
?>
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="home.php">JGCML Grocery</a>
    </div>

    <ul class="nav navbar-top-links navbar-right">
        <li class="navbar-text">Welcome: <strong><?php echo htmlspecialchars($navigationName, ENT_QUOTES, 'UTF-8'); ?></strong></li>
        <?php if (!$isCashierNavigation) { ?>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-label="User menu">
                <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="#myModal" data-toggle="modal"><i class="fa fa-user fa-fw"></i> Add User</a></li>
            </ul>
        </li>
        <?php } ?>
        <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
    </ul>

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <?php if ($isCashierNavigation) { ?>
                    <li>
                        <button type="button" class="nav-menu-toggle" aria-expanded="false"><i class="fa fa-money fa-fw"></i> Select payment method<span class="fa arrow"></span></button>
                        <ul class="nav nav-second-level" style="display: none;">
                            <li><a href="sales.php?id=cash&amp;invoice=<?php echo urlencode($finalcode); ?>">Cash</a></li>
                            <li><a href="sales.php?id=credit&amp;invoice=<?php echo urlencode($finalcode); ?>">Credit</a></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li><a href="home.php"><i class="fa fa-home fa-fw"></i> Home</a></li>
                    <li><a href="products.php"><i class="fa fa-table fa-fw"></i> Product</a></li>
                    <li><a href="customer.php"><i class="fa fa-user fa-fw"></i> Customer</a></li>
                    <li><a href="purchaseslist.php"><i class="fa fa-list-alt fa-fw"></i> Purchase Order List</a></li>
                    <li><a href="orderpo.php"><i class="fa fa-list-alt fa-fw"></i> Purchase Order Form</a></li>
                    <li><a href="supplier.php"><i class="fa fa-truck fa-fw"></i> Supplier</a></li>
                    <li><a rel="facebox" href="select_customer.php"><i class="fa fa-book fa-fw"></i> Customer Ledger</a></li>
                    <li>
                        <button type="button" class="nav-menu-toggle" aria-expanded="false"><i class="fa fa-files-o fa-fw"></i> REPORTS<span class="fa arrow"></span></button>
                        <ul class="nav nav-second-level" style="display: none;">
                            <li><a href="accountreceivables.php">Accounts Receivables Report</a></li>
                            <li><a href="collection.php">Collection Report</a></li>
                            <li><a href="salesreport.php">Sales Report</a></li>
                            <li><a href="inventory.php">Inventory Report</a></li>
                            <li><a href="product_lose.php">List of Product Expired</a></li>
                            <li><a href="returned.php">Report of Returned Products</a></li>
                            <li><a href="search_customer.php">Customer Transaction Record</a></li>
                        </ul>
                    </li>
                    <li>
                        <button type="button" class="nav-menu-toggle" aria-expanded="false"><i class="fa fa-bar-chart-o fa-fw"></i> Charts<span class="fa arrow"></span></button>
                        <ul class="nav nav-second-level" style="display: none;">
                            <li><a href="chart.php">Graph By Category</a></li>
                            <li><a href="charts.php">Graph For Cash and Credit</a></li>
                            <li><a href="lose.php">Graph For Losses</a></li>
                            <li><a href="month_chart.php">Monthly Sales Chart</a></li>
                            <li><a href="yearly_chart.php">Yearly Sales Chart</a></li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<style>
    .sidebar .nav-menu-toggle {
        display: block;
        width: 100%;
        padding: 10px 15px;
        border: 0;
        color: #337ab7;
        background: transparent;
        font: inherit;
        line-height: 1.42857143;
        text-align: left;
    }

    .sidebar .nav-menu-toggle:hover,
    .sidebar .nav-menu-toggle:focus {
        color: #23527c;
        background: #eee;
    }

    .sidebar .nav-second-level {
        display: none !important;
    }

    .sidebar .nav-second-level.nav-open {
        display: block !important;
        height: auto !important;
        overflow: visible !important;
    }
</style>

<script>
    (function () {
        var currentPath = window.location.pathname.replace(/\/$/, '');
        var toggles = document.querySelectorAll('.nav-menu-toggle');

        Array.prototype.forEach.call(toggles, function (toggle) {
            var submenu = toggle.nextElementSibling;
            var links = submenu.querySelectorAll('a');
            var isCurrentSection = Array.prototype.some.call(links, function (link) {
                return link.pathname.replace(/\/$/, '') === currentPath;
            });

            if (isCurrentSection) {
                toggle.setAttribute('aria-expanded', 'true');
                submenu.classList.add('nav-open');
            }
        });

        document.addEventListener('click', function (event) {
            var toggle = event.target;
            while (toggle && (!toggle.classList || !toggle.classList.contains('nav-menu-toggle'))) {
                toggle = toggle.parentElement;
            }
            if (!toggle) {
                return;
            }

            var submenu = toggle.nextElementSibling;
            var isOpen = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            if (isOpen) {
                submenu.classList.remove('nav-open');
            } else {
                submenu.classList.add('nav-open');
            }
        });
    })();
</script>

<?php
if (!$isCashierNavigation) {
    require __DIR__ . '/adduser.php';
}
?>
