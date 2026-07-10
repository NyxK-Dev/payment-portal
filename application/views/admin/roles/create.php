<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Role</title>

    <link rel="stylesheet"
          href="<?= base_url('assets/bootstrap/css/bootstrap.min.css'); ?>">

</head>


<body class="bg-light">


<div class="container mt-5">



    <div class="card shadow-sm mx-auto"
         style="max-width: 600px;">



        <div class="card-header">

            <h5 class="mb-0">

                Role Information

            </h5>

        </div>


        <div class="card-body">



            <form action="<?= site_url('admin/roles/store'); ?>"
                  method="POST">



                <!-- CSRF -->

                <input
                    type="hidden"
                    name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">

                <!-- Role Name -->


                <div class="mb-3">


                    <label for="name"
                           class="form-label">

                        Role Name

                    </label>




                    <input type="text"
                           name="name"
                           id="name"

                           class="form-control <?= isset($errors['name']) ? 'is-invalid' : ''; ?>"

                           placeholder="e.g. manager, operator"

                           value="<?= set_value('name'); ?>"

                           autofocus>




                    <?php if(isset($errors['name'])): ?>

                        <div class="invalid-feedback">

                            <?= $errors['name']; ?>

                        </div>

                    <?php endif; ?>



                </div>

                <!-- Description -->


                <div class="mb-4">


                    <label for="description"
                           class="form-label">

                        Description

                    </label>




                    <textarea
                        name="description"
                        id="description"

                        class="form-control <?= isset($errors['description']) ? 'is-invalid' : ''; ?>"

                        rows="3"

                        placeholder="Brief description of responsibilities..."><?= set_value('description'); ?></textarea>





                    <?php if(isset($errors['description'])): ?>

                        <div class="invalid-feedback">

                            <?= $errors['description']; ?>

                        </div>

                    <?php endif; ?>



                </div>








                <!-- Buttons -->


                <div class="text-end">


                    <a href="<?= site_url('admin/roles'); ?>"
                       class="btn btn-secondary me-2">

                        Cancel

                    </a>




                    <button type="submit"
                            class="btn btn-primary">

                        Save Role

                    </button>



                </div>





            </form>




        </div>


    </div>


</div>



</body>

</html>