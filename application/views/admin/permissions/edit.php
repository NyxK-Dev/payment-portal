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

                <input
                    type="hidden"
                    name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="mb-3">


                    <label class="form-label">
                        Code
                    </label>


                    <input type="text"
                        name="code"
                        class="form-control"
                        value="<?= $permission->code ?>"
                        required>


                </div>





                <div class="mb-3">


                    <label class="form-label">
                        Name
                    </label>


                    <input type="text"
                        name="name"
                        class="form-control"
                        value="<?= $permission->name ?>"
                        required>


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