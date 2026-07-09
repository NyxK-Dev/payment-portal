<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Role</title>


    <link rel="stylesheet"
        href="<?= base_url('assets/bootstrap/css/bootstrap.min.css'); ?>">


</head>


<body class="bg-light">


    <div class="container mt-5">



        <div class="card shadow-sm mx-auto"
            style="max-width:600px;">



            <div class="card-header">

                <h5 class="mb-0">

                    Update Role Information

                </h5>

            </div>





            <div class="card-body">



                <form action="<?= site_url('admin/roles/update/' . $role->id); ?>"
                    method="post">





                    <input
                        type="hidden"
                        name="<?= $this->security->get_csrf_token_name(); ?>"
                        value="<?= $this->security->get_csrf_hash(); ?>">


                    <!-- Name -->


                    <div class="mb-3">


                        <label class="form-label">

                            Role Name

                        </label>




                        <input
                            type="text"
                            name="name"

                            class="form-control <?= isset($errors['name']) ? 'is-invalid' : ''; ?>"


                            value="<?= set_value('name', $role->name); ?>">





                        <?php if (isset($errors['name'])): ?>

                            <div class="invalid-feedback">

                                <?= $errors['name']; ?>

                            </div>

                        <?php endif; ?>



                    </div>








                    <!-- Description -->


                    <div class="mb-4">


                        <label class="form-label">

                            Description

                        </label>




                        <textarea
                            name="description"

                            rows="3"

                            class="form-control <?= isset($errors['description']) ? 'is-invalid' : ''; ?>"><?= set_value('description', $role->description); ?></textarea>





                        <?php if (isset($errors['description'])): ?>

                            <div class="invalid-feedback">

                                <?= $errors['description']; ?>

                            </div>

                        <?php endif; ?>



                    </div>







                    <div class="text-end">


                        <a href="<?= site_url('admin/roles'); ?>"
                            class="btn btn-secondary me-2">

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