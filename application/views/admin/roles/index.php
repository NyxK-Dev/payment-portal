<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management</title>
    
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css'); ?>">
    
    </head>
<body class="bg-light">

<div class="container mt-5">
    
    <div class="row align-items-center mb-4">
       
        <div class=" text-right text-end">
            <a href="<?= site_url('admin/roles/create'); ?>" class="btn btn-primary">
                Add Role
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white font-weight-bold">
            Roles
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="thead-light table-light">
                    <tr>
                        <th style="width: 15%;">ID</th>
                        <th style="width: 45%;">Name</th>
                        <th style="width: 40%; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td><?= $role->id; ?></td>
                                <td>
                                    <span class="badge badge-info bg-info text-dark">
                                        <?= ucfirst($role->name); ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="<?= site_url('admin/roles/edit/' . $role->id); ?>" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <a href="<?= site_url('admin/roles/permissions/' . $role->id); ?>" class="btn btn-sm btn-info">
                                        Permissions
                                    </a>
                                    <a href="<?= site_url('admin/roles/delete/' . $role->id); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this role?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                No roles found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>