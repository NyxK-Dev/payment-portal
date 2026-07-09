<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Role</title>
    
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css'); ?>">
</head>
<body class="bg-light">

<div class="container mt-5">
    
    <div class="row align-items-center mb-4">
     
     
    </div>

    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-header bg-white font-weight-bold">
            Role Information
        </div>
        <div class="card-body">
            
            <form action="<?= site_url('admin/roles/store'); ?>" method="POST">
               <input
        type="hidden"
        name="<?= $this->security->get_csrf_token_name(); ?>"
        value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="form-group mb-3">
                    <label for="name" class="form-label font-weight-bold mb-1">Role Name</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-control" 
                           placeholder="e.g. manager, operator" 
                           required 
                           autofocus>
                </div>

                <div class="form-group mb-4">
                    <label for="description" class="form-label font-weight-bold mb-1">Description</label>
                    <textarea name="description" 
                              id="description" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Brief description of responsibilities..."></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= site_url('admin/roles'); ?>" class="btn btn-light me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Role</button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>