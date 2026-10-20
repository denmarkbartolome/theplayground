
<?php
$money = static function ($amount): string {
    return '₱' . number_format((float) $amount, 2);
};

$periodLabel = [
    'daily' => 'Daily Revenue Report',
    'weekly' => 'Weekly Revenue Report',
    'monthly' => 'Monthly Revenue Report'
][$period] ?? 'Revenue Report';

$rangeLabel = date('M d, Y', strtotime($startDate));

if ($startDate !== $endDate) {
    $rangeLabel .= ' - ' . date('M d, Y', strtotime($endDate));
}
?>

<style>
    .revenue-card {
        border-radius: 8px;
        min-height: 135px;
    }

    .revenue-value {
        font-size: 1.65rem;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .report-heading {
        font-size: 1.25rem;
        font-weight: 600;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        body * {
            visibility: hidden !important;
        }

        #printArea,
        #printArea * {
            visibility: visible !important;
        }

        #printArea {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .no-print {
            display: none !important;
        }

        .card,
        .revenue-card {
            box-shadow: none !important;
            break-inside: avoid;
        }

        .table {
            width: 100%;
        }

        .report-heading {
            font-size: 20px;
        }

        body {
            background: #fff !important;
        }
    }
</style>

<div class="content-header no-print">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Revenue Reports</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="<?= BASE_URL ?>/Dashboard">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- Report filters -->
        <div class="card card-outline card-primary no-print">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-1"></i>
                    Report Filters
                </h3>
            </div>

            <form method="GET" action="<?= BASE_URL ?>/Reports">
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="period">Report Type</label>
                                <select
                                    name="period"
                                    id="period"
                                    class="form-control"
                                >
                                    <option value="daily"
                                        <?= $period === 'daily' ? 'selected' : '' ?>>
                                        Daily
                                    </option>
                                    <option value="weekly"
                                        <?= $period === 'weekly' ? 'selected' : '' ?>>
                                        Weekly
                                    </option>
                                    <option value="monthly"
                                        <?= $period === 'monthly' ? 'selected' : '' ?>>
                                        Monthly
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4" id="dateFilter">
                            <div class="form-group">
                                <label for="date">Select Date</label>
                                <input
                                    type="date"
                                    name="date"
                                    id="date"
                                    class="form-control"
                                    value="<?= htmlspecialchars($selectedDate) ?>"
                                >
                                <small class="text-muted">
                                    For weekly reports, this selects the week.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-4" id="monthFilter">
                            <div class="form-group">
                                <label for="month">Select Month</label>
                                <input
                                    type="month"
                                    name="month"
                                    id="month"
                                    class="form-control"
                                    value="<?= htmlspecialchars($selectedMonth) ?>"
                                >
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i>
                        Generate Report
                    </button>

                    <a href="<?= BASE_URL ?>/Reports"
                       class="btn btn-default">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Printable report starts here -->
        <div id="printArea">

            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="report-heading mb-1">
                        Revenue Report
                    </h2>

                    <div class="text-muted">
                        <?= htmlspecialchars($periodLabel) ?>
                    </div>

                    <div>
                        <strong>Reporting Period:</strong>
                        <?= htmlspecialchars($rangeLabel) ?>
                    </div>
                </div>

                <div class="text-right">
                    <div>
                        <strong>Generated:</strong>
                        <?= date('M d, Y h:i A') ?>
                    </div>
                    <div class="text-muted">
                        Admin Report
                    </div>
                </div>
            </div>

            <!-- Revenue summary cards -->
            <div class="row">

                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-primary revenue-card">
                        <div class="inner">
                            <h4 class="revenue-value">
                                <?= $money($courtTotal) ?>
                            </h4>
                            <p>Normal Court Bookings</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-info revenue-card">
                        <div class="inner">
                            <h4 class="revenue-value">
                                <?= $money($addonTotal) ?>
                            </h4>
                            <p>Paddle Rental Add-ons</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-success revenue-card">
                        <div class="inner">
                            <h4 class="revenue-value">
                                <?= $money($openPlayTotal) ?>
                            </h4>
                            <p>Open Play</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-dark revenue-card">
                        <div class="inner">
                            <h4 class="revenue-value">
                                <?= $money($totalRevenue) ?>
                            </h4>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Detailed report table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Revenue Breakdown
                    </h3>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Revenue Source</th>
                                <th class="text-center">Transactions</th>
                                <th class="text-right">Revenue</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Normal Court Bookings</td>
                                <td class="text-center">
                                    <?= number_format($paidBookings) ?>
                                </td>
                                <td class="text-right">
                                    <?= $money($courtTotal) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>Paddle Rental Add-ons</td>
                                <td class="text-center">
                                    —
                                </td>
                                <td class="text-right">
                                    <?= $money($addonTotal) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>Open Play Reservations</td>
                                <td class="text-center">
                                    <?= number_format($confirmedReservations) ?>
                                </td>
                                <td class="text-right">
                                    <?= $money($openPlayTotal) ?>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">
                                    TOTAL REVENUE
                                </th>
                                <th class="text-right">
                                    <?= $money($totalRevenue) ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="text-muted small mt-3">
                Revenue includes paid normal bookings and verified,
                confirmed Open Play reservations within the selected period.
                Paddle rental revenue is shown separately from court revenue.
            </div>

        </div>
        <!-- Printable report ends here -->

        <div class="mb-3 no-print">
            <button type="button"
                    class="btn btn-success"
                    onclick="window.print()">
                <i class="fas fa-print mr-1"></i>
                Print Report
            </button>
        </div>

    </div>
</section>

<script>
(function () {
    const period = document.getElementById('period');
    const dateFilter = document.getElementById('dateFilter');
    const monthFilter = document.getElementById('monthFilter');
    const dateInput = document.getElementById('date');
    const monthInput = document.getElementById('month');

    function updateFilters() {
        const isMonthly = period.value === 'monthly';

        monthFilter.style.display = isMonthly ? '' : 'none';
        dateFilter.style.display = isMonthly ? 'none' : '';

        dateInput.disabled = isMonthly;
        monthInput.disabled = !isMonthly;
    }

    period.addEventListener('change', updateFilters);
    updateFilters();
})();
</script>
