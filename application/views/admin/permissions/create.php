<div class="container mt-5">

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">Create Permission</h5>
        </div>


        <div class="card-body">


            <form action="<?= site_url('admin/permissions/store'); ?>" method="post">
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
                        placeholder="Example: user_create"
                        required>

                </div>



                <div class="mb-3">

                    <label class="form-label">
                        Name
                    </label>

                    <input type="text"
                        name="name"
                        class="form-control"
                        placeholder="Example: Create User"
                        required>

                </div>



                <div class="d-flex justify-content-end">


                    <a href="<?= site_url('admin/permissions'); ?>"
                        class="btn btn-secondary me-2">

                        Cancel

                    </a>


                    <button type="submit"
                        class="btn btn-primary">

                        Save

                    </button>


                </div>



            </form>


        </div>

    </div>

</div>