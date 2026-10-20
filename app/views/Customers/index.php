<?php
$e = static function ($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
};
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Customers Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="<?= $e(BASE_URL) ?>/Dashboard">
                            Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Customers</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible">
                <?= $e($success) ?>
                <button type="button" class="close"
                        data-dismiss="alert" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible">
                <?= $e($error) ?>
                <button type="button" class="close"
                        data-dismiss="alert" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-1"></i>
                    Customer List
                </h3>

                <div class="card-tools">
                    <button type="button"
                            class="btn btn-primary btn-sm"
                            data-toggle="modal"
                            data-target="#addCustomerModal">
                        <i class="fas fa-user-plus"></i>
                        Add Customer
                    </button>
                </div>
            </div>

            <div class="card-body">

                <form method="GET"
                      action="<?= $e(BASE_URL) ?>/Customers"
                      class="form-row mb-3">

                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Search customer name or email..."
                                   value="<?= $e($search) ?>">

                            <div class="input-group-append">
                                <button class="btn btn-primary"
                                        type="submit">
                                    <i class="fas fa-search"></i>
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="filter"
                                class="form-control"
                                onchange="this.form.submit()">
                            <option value="all"
                                <?= $filter === 'all' ? 'selected' : '' ?>>
                                Name: A–Z
                            </option>
                            <option value="recent"
                                <?= $filter === 'recent' ? 'selected' : '' ?>>
                                Newest customers
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <a href="<?= $e(BASE_URL) ?>/Customers"
                           class="btn btn-default">
                            <i class="fas fa-sync-alt"></i>
                            Reset
                        </a>
                    </div>
                </form>

                <p class="text-muted">
                    Customers found:
                    <strong><?= count($customers) ?></strong>
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 70px;">#</th>
                                <th>Customer Name</th>
                                <th>Email Address</th>
                                <th>Registration Date</th>
                            </tr>
                        </thead>

                        <tbody>
    <?php if (empty($customers)): ?>
        <tr>
            <td colspan="4" class="text-center text-muted py-4">
                <i class="fas fa-users fa-2x mb-2"></i>
                <p class="mb-0">No customers found.</p>
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($customers as $index => $customer): ?>
            <tr>
                <td><?= $index + 1 ?></td>

                <td>
                    <i class="fas fa-user-circle text-secondary mr-1"></i>
                    <?= $e(
                        $customer->first_name . ' ' .
                        $customer->last_name
                    ) ?>
                </td>

                <td><?= $e($customer->email) ?></td>

                <td>
                    <?php if (!empty($customer->created_at)): ?>
                        <?= $e(date(
                            'M d, Y',
                            strtotime($customer->created_at)
                        )) ?>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Add Customer Modal -->
<div class="modal fade"
     id="addCustomerModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="addCustomerLabel"
     aria-hidden="true">

    <div class="modal-dialog" role="document">
        <form method="POST"
              action="<?= $e(BASE_URL) ?>/Customers/create">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerLabel">
                        <i class="fas fa-user-plus mr-1"></i>
                        Add Customer
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text"
                               id="first_name"
                               name="first_name"
                               class="form-control"
                               maxlength="100"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text"
                               id="last_name"
                               name="last_name"
                               class="form-control"
                               maxlength="100"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="customer_email">Email Address</label>
                        <input type="email"
                               id="customer_email"
                               name="email"
                               class="form-control"
                               maxlength="255"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="customer_password">Password</label>
                        <input type="password"
                               id="customer_password"
                               name="password"
                               class="form-control"
                               minlength="8"
                               autocomplete="new-password"
                               required>
                        <small class="form-text text-muted">
                            Set a password for the customer's account.
                        </small>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        The account role will automatically be set to
                        <strong>Customer</strong>.
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Create Customer
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
