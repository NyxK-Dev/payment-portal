<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role</title>

    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css'); ?>">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="row align-items-center mb-4">
       

   
    </div>

    <div class="card shadow-sm mx-auto" style="max-width:600px;">

        <div class="card-header bg-white fw-bold">
            Update Role Information
        </div>

        <div class="card-body">

            <form action="<?= site_url('admin/roles/update/'.$role->id); ?>" method="post">

                <input
                    type="hidden"
                    name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="form-group mb-3">
                    <label for="name" class="fw-bold mb-1">
                        Role Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        value="<?= html_escape($role->name); ?>"
                        required>
                </div>

                <div class="form-group mb-4">
                    <label for="description" class="fw-bold mb-1">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        rows="3"><?= html_escape($role->description); ?></textarea>
                </div>

                <div class="d-flex justify-content-end">

                    <a href="<?= site_url('admin/roles'); ?>"
                       class="btn btn-light me-2">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        Update Role
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>