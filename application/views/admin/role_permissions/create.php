<div class="container mt-4">


    <div class="card shadow-sm">


       <div class="card-header">

    <h5 class="mb-0">
        Assign Permissions To Role
    </h5>

</div>




        <div class="card-body">


            <form action="<?= site_url('admin/role_permissions/store'); ?>"
                method="post">


                <!-- CSRF -->
                <input
                    type="hidden"
                    name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">





                <!-- Role -->

                <div class="mb-4">


                    <label class="form-label fw-bold">

                        Role

                    </label>



                    <select name="role_id"
                        class="form-select"
                        required>


                        <option value="">

                            Select Role

                        </option>



                        <?php foreach ($roles as $role): ?>


                            <option value="<?= $role->id ?>">

                                <?= $role->name ?>

                            </option>


                        <?php endforeach; ?>


                    </select>


                </div>






                <!-- Permissions -->


                <div class="mb-4">


                    <div class="d-flex justify-content-between align-items-center mb-2">


                        <label class="form-label fw-bold mb-0">

                            Permissions

                        </label>



                        <div class="form-check">


                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="selectAll">



                            <label class="form-check-label">

                                Select All

                            </label>


                        </div>


                    </div>





                    <div class="border rounded p-3">


                        <div class="row">


                        <?php foreach ($permissions as $permission): ?>


                            <div class="col-md-4 mb-2">


                                <div class="form-check">


                                    <input
                                        class="form-check-input permission-checkbox"
                                        type="checkbox"
                                        name="permission_id[]"
                                        value="<?= $permission->id ?>">



                                    <label class="form-check-label">

                                        <?= $permission->name ?>

                                    </label>


                                </div>


                            </div>



                        <?php endforeach; ?>


                        </div>


                    </div>



                </div>






                <!-- Buttons -->


                <div class="text-end">


                    <a href="<?= site_url('admin/role_permissions'); ?>"
                        class="btn btn-secondary me-2">

                        Cancel

                    </a>



                    <button type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-save"></i>
                        Save

                    </button>


                </div>



            </form>



        </div>


    </div>


</div>






<script>

document
.getElementById('selectAll')
.addEventListener('change', function(){


    document
    .querySelectorAll('.permission-checkbox')
    .forEach(function(item){


        item.checked =
            this.checked;


    }.bind(this));


});

</script>