<div class="container mt-4">


    <div class="card shadow-sm">



        <div class="card-header">

            <h5 class="mb-0">

                Edit Role Permissions

            </h5>

        </div>





        <div class="card-body">


            <form action="<?= site_url('admin/role_permissions/update/' . $role_id); ?>"
                method="post">



                <!-- CSRF -->

                <input
                    type="hidden"
                    name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">






                <!-- Role -->


                <div class="mb-3">


                    <label class="form-label">

                        Role

                    </label>




                    <select class="form-control"
                        disabled>



                        <?php foreach($roles as $role): ?>


                            <?php if($role->id == $role_id): ?>


                                <option selected>

                                    <?= $role->name ?>

                                </option>


                            <?php endif; ?>


                        <?php endforeach; ?>



                    </select>


                </div>








                <!-- Permissions -->


                <div class="mb-3">


                    <div class="d-flex justify-content-between align-items-center">


                        <label class="form-label mb-2">

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



                            <?php foreach($permissions as $permission): ?>


                                <div class="col-md-4 mb-2">


                                    <div class="form-check">


                                        <input
                                            class="form-check-input permission-checkbox"
                                            type="checkbox"
                                            name="permission_id[]"
                                            value="<?= $permission->id ?>"



                                            <?= in_array(
                                                $permission->id,
                                                $assigned_permissions
                                            ) ? 'checked' : '' ?>

                                        >



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


                <div class="d-flex justify-content-end">


                    <a href="<?= site_url('admin/role_permissions'); ?>"
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







<script>


document
.getElementById('selectAll')
.addEventListener('change', function(){


    let permissions =
        document.querySelectorAll(
            '.permission-checkbox'
        );



    permissions.forEach(function(permission){


        permission.checked =
            document.getElementById('selectAll').checked;


    });


});



</script>