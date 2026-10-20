<?php
$e = static fn($value) => htmlspecialchars(
    (string) $value,
    ENT_QUOTES,
    'UTF-8'
);
?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible">
        <?= $e($success) ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible">
        <?= $e($error) ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">User Management</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" id="btnNew">
                <i class="fas fa-plus"></i> New User
            </button>
        </div>
    </div>

    <div class="card-body">
        <table id="users-table"
               class="table table-bordered table-striped table-hover w-100">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-center">Options</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (($users ?? []) as $user): ?>
                <?php
                $get = static fn($key) =>
                    is_object($user)
                        ? ($user->$key ?? '')
                        : ($user[$key] ?? '');

                $id = (string) $get('id');
                $first = (string) $get('first_name');
                $last = (string) $get('last_name');
                $email = (string) $get('email');
                $role = (string) $get('role');

                $roleClass = [
                    'admin' => 'danger',
                    'staff' => 'warning',
                    'customer' => 'info',
                ][$role] ?? 'secondary';
                ?>
                <tr>
                    <td><?= $e(trim($first . ' ' . $last)) ?></td>
                    <td><?= $e($email) ?></td>
                    <td>
                        <span class="badge badge-<?= $e($roleClass) ?>">
                            <?= $e(ucfirst($role)) ?>
                        </span>
                    </td>
                    
<td class="text-center text-nowrap">
    <button type="button"
            class="btn btn-warning btn-sm edit"
            data-id="<?= $e($id) ?>">
        <i class="fas fa-edit"></i> Edit
    </button>

    <button type="button"
            class="btn btn-danger btn-sm delete"
            data-id="<?= $e($id) ?>">
        <i class="fas fa-trash"></i> Delete
    </button>
</td>

                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="user-modal" tabindex="-1">
    <div class="modal-dialog">
        <form id="users-form" method="POST"
              action="<?= $e(BASE_URL . '/users/create') ?>"
              class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Add User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="csrf_token"
                       value="<?= $e($csrfToken ?? '') ?>">
                <input type="hidden" name="id" id="id">

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name"
                           class="form-control" maxlength="100" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name"
                           class="form-control" maxlength="100" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email"
                           class="form-control" maxlength="255" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password"
                           class="form-control" minlength="8"
                           autocomplete="new-password">
                    <small class="text-muted" id="password-help">
                        Required for new users. At least 8 characters.
                    </small>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="customer">Customer</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnSave">
                    <i class="fas fa-save"></i> Save User
                </button>
            </div>
        </form>
    </div>
</div>
