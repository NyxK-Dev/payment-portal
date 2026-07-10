<div class="container mt-5">


    <div class="d-flex justify-content-between mb-4">


        <a href="<?= site_url('admin/role_permissions/create'); ?>"
            class="btn btn-primary ms-auto">

            Assign Permission

        </a>


    </div>



    <div class="card shadow-sm">


        <div class="card-body">


            <table class="table table-bordered">


                <thead>

                    <tr>

                        <th>Role</th>

                        <th>Permissions</th>

                        <th width="200">Action</th>

                    </tr>

                </thead>



                <tbody>


                <?php foreach ($role_permissions as $item): ?>


                    <tr>


                        <td>

                            <?= $item->role_name ?>

                        </td>



                        <td>


                            <?php 
                            $permissions = explode(',', $item->permissions);
                            ?>


                            <?php foreach ($permissions as $permission): ?>


                                <span class="badge bg-info me-1">

                                    <?= $permission ?>

                                </span>


                            <?php endforeach; ?>


                        </td>




                        <td>


                            <a href="<?= site_url('admin/role_permissions/edit_role/' . $item->role_id); ?>"
                                class="btn btn-warning btn-sm">

                                Edit

                            </a>




                          <button 
    class="btn btn-warning btn-sm change-role"
    data-id="<?= $user->id ?>"
    data-name="<?= $user->name ?>"
    data-role="<?= $user->role_id ?>"
    data-bs-toggle="modal"
    data-bs-target="#roleModal">

    Change Role

</button>


                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>


            </table>


        </div>


    </div>


</div>