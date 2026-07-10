<div class="container-fluid">


    <!-- Validation Error -->
    <?php if (validation_errors()): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?= validation_errors(); ?>


            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>


        </div>

    <?php endif; ?>




    <div class="card card-primary card-outline">


        <div class="card-header">

            <h5 class="card-title">

                <i class="bi bi-folder-plus me-2"></i>

                Create Lookup Group

            </h5>


        </div>




        <form method="post"
            action="<?= site_url('admin/lookupgroups/store'); ?>">



            <input type="hidden"
                name="<?= $this->security->get_csrf_token_name(); ?>"
                value="<?= $this->security->get_csrf_hash(); ?>">





            <div class="card-body">


                <div class="row">



                    <!-- Code -->
                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">

                            Code

                        </label>



                        <div class="input-group">


                            <span class="input-group-text">

                                <i class="bi bi-code-square"></i>

                            </span>



                            <input
                                type="text"
                                name="code"
                                class="form-control"
                                placeholder="Enter group code"
                                value="<?= set_value('code'); ?>">


                        </div>

                    </div>






                    <!-- Name -->
                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">

                            Name

                        </label>



                        <div class="input-group">


                            <span class="input-group-text">

                                <i class="bi bi-tag"></i>

                            </span>



                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                placeholder="Enter group name"
                                value="<?= set_value('name'); ?>">


                        </div>


                    </div>







                    <!-- Description -->
                    <div class="col-md-12 mb-3">


                        <label class="form-label fw-bold">

                            Description

                        </label>



                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                            placeholder="Enter group description"><?= set_value('description'); ?></textarea>



                    </div>




                </div>


            </div>







            <div class="card-footer text-end">


                <a href="<?= site_url('admin/lookupgroups'); ?>"
                    class="btn btn-secondary me-2">


                    <i class="bi bi-arrow-left"></i>

                    Cancel


                </a>





                <button type="submit"
                    class="btn btn-primary">


                    <i class="bi bi-check-circle"></i>

                    Save


                </button>



            </div>





        </form>


    </div>


</div>