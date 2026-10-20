<?php
$h = static fn($value) => htmlspecialchars(
    (string) $value,
    ENT_QUOTES,
    'UTF-8'
);
?>

<section class="content-header">
    <div class="container-fluid">
        <h1>Court Management</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible">
                <?= $h($success) ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible">
                <?= $h($error) ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus mr-1"></i>
                    Add New Court
                </h3>
            </div>

            <form method="POST" action="<?= BASE_URL ?>/Courts/create">
                <div class="card-body">
                    <div class="form-group">
                        <label for="court_name">Court Name</label>
                        <input
                            type="text"
                            name="court_name"
                            id="court_name"
                            class="form-control"
                            maxlength="100"
                            placeholder="e.g. Court 1"
                            required
                        >
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Add Court
                    </button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Courts</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">
                        <?= count($courts) ?> courts
                    </span>
                </div>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Court Name</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th style="min-width: 240px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (empty($courts)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No courts have been added yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courts as $court): ?>
                            <?php
                            $active = filter_var(
                                $court['is_active'],
                                FILTER_VALIDATE_BOOLEAN
                            );
                            ?>
                            <tr>
                                <td><?= $h($court['id']) ?></td>

                                <td>
                                    <strong>
                                        <?= $h($court['court_name']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php if ($active): ?>
                                        <span class="badge badge-success">
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= $h($court['created_at'] ?? '—') ?>
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-info"
                                        data-toggle="modal"
                                        data-target="#editCourtModal"
                                        data-id="<?= $h($court['id']) ?>"
                                        data-name="<?= $h($court['court_name']) ?>"
                                    >
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <form
                                        method="POST"
                                        action="<?= BASE_URL ?>/Courts/toggleStatus"
                                        class="d-inline"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $h($court['id']) ?>"
                                        >
                                        <input
                                            type="hidden"
                                            name="is_active"
                                            value="<?= $active ? '0' : '1' ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-sm <?= $active ? 'btn-warning' : 'btn-success' ?>"
                                            onclick="return confirm('<?= $active ? 'Deactivate' : 'Activate' ?> this court?')"
                                        >
                                            <i class="fas <?= $active ? 'fa-ban' : 'fa-check' ?>"></i>
                                            <?= $active ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<!-- Edit Court Modal -->
<div class="modal fade" id="editCourtModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="<?= BASE_URL ?>/Courts/update">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Court</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_court_id">

                    <div class="form-group">
                        <label for="edit_court_name">Court Name</label>
                        <input
                            type="text"
                            name="court_name"
                            id="edit_court_name"
                            class="form-control"
                            maxlength="100"
                            required
                        >
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    $('#editCourtModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);

        $('#edit_court_id').val(button.data('id'));
        $('#edit_court_name').val(button.data('name'));
    });
});
</script>
