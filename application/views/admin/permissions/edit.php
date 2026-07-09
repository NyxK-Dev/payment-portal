<div class="container mt-5">

    <div class="card shadow-sm">


        <div class="card-header">

            <h5 class="mb-0">
                Edit Permission
            </h5>

        </div>



        <div class="card-body">


            <form action="<?= site_url('admin/permissions/update/' . $permission->id); ?>"
                  method="post">


                <!-- CSRF -->

                <input
                    type="hidden"
                    name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">






                <!-- Code -->

                <div class="mb-3">


                    <label class="form-label">
                        Code
                    </label>



                    <input
                        type="text"
                        name="code"
                        class="form-control <?= isset($errors['code']) ? 'is-invalid' : ''; ?>"
                        value="<?= set_value('code', $permission->code); ?>">



                    <?php if(isset($errors['code'])): ?>

                        <div class="invalid-feedback">

                            <?= $errors['code']; ?>

                        </div>

                    <?php endif; ?>


                </div>








                <!-- Name -->

                <div class="mb-3">


                    <label class="form-label">
                        Name
                    </label>




                    <input
                        type="text"
                        name="name"
                        class="form-control <?= isset($errors['name']) ? 'is-invalid' : ''; ?>"
                        value="<?= set_value('name', $permission->name); ?>">





                    <?php if(isset($errors['name'])): ?>

                        <div class="invalid-feedback">

                            <?= $errors['name']; ?>

                        </div>

                    <?php endif; ?>



                </div>








                <!-- Description -->

                <div class="mb-3">


                    <label class="form-label">
                        Description
                    </label>




                    <textarea
                        name="description"
                        class="form-control <?= isset($errors['description']) ? 'is-invalid' : ''; ?>"
                        rows="3"><?= set_value('description', $permission->description ?? ''); ?></textarea>





                    <?php if(isset($errors['description'])): ?>

                        <div class="invalid-feedback">

                            <?= $errors['description']; ?>

                        </div>

                    <?php endif; ?>



                </div>








                <div class="d-flex justify-content-end">


                    <a href="<?= site_url('admin/permissions'); ?>"
                       class="btn btn-secondary me-2">

                        Cancel

                    </a>




                    <button type="submit"
                            class="btn btn-primary">

                        Update

                    </button>


                </div>



            </form>


        </div>


    </div>


</div>