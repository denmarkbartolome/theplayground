<?php
    $title = $title ?? 'Dashboard';

    $sessionUser = $_SESSION['user'] ?? [];
    $firstName   = $sessionUser['first_name'] ?? '';
    $lastName    = $sessionUser['last_name'] ?? '';
    $displayName = trim($firstName . ' ' . $lastName) ?: 'Guest';
    $initial     = strtoupper(substr($displayName, 0, 1));
    $role        = ucfirst($sessionUser['role'] ?? 'customer');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'MVC Application') ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/plugins/toastr/toastr.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/plugins/daterangepicker/daterangepicker.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/sidebar.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link">

                </span>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#"
                   role="button" aria-expanded="false" title="Account settings">
                    <i class="fas fa-user-cog fa-lg"></i>
                </a>

                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
                    <span class="dropdown-item dropdown-header">
                        <strong><?= htmlspecialchars($displayName) ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($role) ?></small>
                    </span>
                    <div class="dropdown-divider"></div>

                    <a href="<?= BASE_URL ?>/MyProfile" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> My Profile
                    </a>

                    <div class="dropdown-divider"></div>

                    <form action="<?= BASE_URL ?>/Auth/logout" method="POST" class="m-0">
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-light-primary playground-sidebar">
        <a href="<?= BASE_URL ?>/" class="brand-link">
            <span class="brand-text font-weight-bold">The Play Ground</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/" class="nav-link <?= $title === 'Dashboard' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-th-large icon-dashboard" style="color: gray;"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">Booking Information</li>

                    <?php if (($_SESSION['user']['role'] ?? '') !== 'admin'): ?>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/booking"
                               class="nav-link <?= $title === 'Bookings' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-calendar-check icon-court" style="color: gray;"></i>
                                <p>Book a Court</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['user']['role'] ?? '', ['staff', 'customer'], true)): ?>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/OpenPlay"
                               class="nav-link <?= ($title ?? '') === 'Open Play' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-table-tennis icon-play" style="color: gray;"></i>
                                <p>Open Play</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['user']['role'] ?? '', ['admin', 'staff'], true)): ?>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/allbookings"
                               class="nav-link <?= $title === 'All Bookings' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-list-alt icon-play" style="color: gray;"></i>
                                <p>All Bookings</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['user']['role'] ?? '', ['admin', 'staff'], true)): ?>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/OpenPlayReservations"
                               class="nav-link <?= ($title ?? '') === 'Open Play Reservations' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-calendar-check" style="color: gray;"></i>
                                <p>Open Play Reservations</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
    <li class="nav-item">
        <a href="<?= BASE_URL ?>/courts"
           class="nav-link <?= ($title ?? '') === 'Court Management' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-table-tennis" style="color: gray;"></i>
            <p>Court Management</p>
        </a>
    </li>
<?php endif; ?>

                    <?php if (($_SESSION['user']['role'] ?? '') === 'customer'): ?>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/mybookings"
                               class="nav-link <?= $title === 'My Bookings' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-calendar-check icon-play" style="color: gray;"></i>
                                <p>My Bookings</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (($_SESSION['user']['role'] ?? '') === 'customer'): ?>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/MyOPReservations"
                                class="nav-link <?= ($title ?? '') === 'My O.P Reservations' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-clipboard-list icon-play" style="color: gray;"></i>
                                <p>My Open Play</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
    <li class="nav-item">
        <a href="<?= BASE_URL ?>/Customers"
           class="nav-link <?= ($title ?? '') === 'Customers' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-users" style="color: gray;"></i>
            <p>Customers</p>
        </a>
    </li>
<?php endif; ?>

                    <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/users"
                               class="nav-link <?= ($title ?? '') === 'Users' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-user-circle icon-profile" style="color: gray;"></i>
                                <p>Users</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/reports"
                               class="nav-link <?= ($title ?? '') === 'Reports' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chart-bar" style="color: gray;"></i>
                                <p>Reports</p>
                            </a>
                        </li>
                    <?php endif; ?>



                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <h1><?= htmlspecialchars($title ?? '') ?></h1>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <?= $content ?>
            </div>
        </section>
    </div>

</div>

<!-- jQuery -->
<script src="<?= BASE_URL ?>/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= BASE_URL ?>/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?= BASE_URL ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= BASE_URL ?>/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="../../plugins/toastr/toastr.min.js"></script>
<!-- daterangepicker -->
<script src="<?= BASE_URL ?>/plugins/moment/moment.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= BASE_URL ?>/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="<?= BASE_URL ?>/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/jszip/jszip.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?= BASE_URL ?>/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?= BASE_URL ?>/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script src="<?= BASE_URL ?>/dist/js/adminlte.js"></script>
<script src="<?= BASE_URL ?>/js/app.js"></script>
<script src="<?= BASE_URL ?>/public/js/<?= $title ?? 'Dashboard' ?>.js"></script>

</body>
</html>