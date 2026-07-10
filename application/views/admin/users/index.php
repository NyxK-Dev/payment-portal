<div class="container mt-5">


    <div class="d-flex justify-content-between mb-4">

        <h4>
            Users
        </h4>

    </div>




    <div class="card shadow-sm">


        <div class="card-body">


            <table class="table table-bordered">


                <thead>

                    <tr>

                        <th width="80">
                            ID
                        </th>


                        <th>
                            Name
                        </th>


                        <th>
                            Email
                        </th>


                        <th>
                            Role
                        </th>


                        <th>
                            Status
                        </th>


                        <th width="200">
                            Action
                        </th>


                    </tr>

                </thead>




                <tbody>


                    <?php foreach ($users as $user): ?>


                        <tr>


                            <td>

                                <?= $user->id ?>

                            </td>



                            <td>

                                <?= $user->name ?>

                            </td>



                            <td>

                                <?= $user->email ?>

                            </td>



                            <td>

                                <span class="badge bg-info">

                                    <?= $user->role_name ?>

                                </span>


                            </td>



                            <td>


                                <?php if ($user->status_lookup_id == 1): ?>


                                    <span class="badge bg-success">

                                        Active

                                    </span>


                                <?php else: ?>


                                    <span class="badge bg-danger">

                                        Inactive

                                    </span>


                                <?php endif; ?>


                            </td>




                            <td>


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





<!-- Change Role Modal -->


<div class="modal fade" id="roleModal">


    <div class="modal-dialog">


        <div class="modal-content">



            <form method="post" id="roleForm">

                <input
                    type="hidden"
                    name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="modal-header">


                    <h5 class="modal-title">

                        Change User Role

                    </h5>



                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">

                    </button>



                </div>





                <div class="modal-body">



                    <div class="mb-3">


                        <label class="form-label">

                            User

                        </label>



                        <input
                            type="text"
                            id="user_name"
                            class="form-control"
                            readonly>


                    </div>





                    <div class="mb-3">


                        <label class="form-label">

                            Role

                        </label>



                        <select
                            name="role_id"
                            id="role_id"
                            class="form-control">


                            <?php foreach ($roles as $role): ?>


                                <option value="<?= $role->id ?>">


                                    <?= $role->name ?>


                                </option>



                            <?php endforeach; ?>


                        </select>



                    </div>




                </div>





                <div class="modal-footer">


                    <button
                        type="submit"
                        class="btn btn-primary">

                        Save Change

                    </button>


                </div>




            </form>



        </div>


    </div>


</div>






<script>
    document
        .querySelectorAll('.change-role')
        .forEach(button => {



            button.addEventListener(
                'click',
                function() {



                    let id =
                        this.dataset.id;



                    let name =
                        this.dataset.name;



                    let role =
                        this.dataset.role;




                    document
                        .getElementById('user_name')
                        .value = name;



                    document
                        .getElementById('role_id')
                        .value = role;



                    document
                        .getElementById('roleForm')
                        .action =
                        "<?= site_url('admin/users/updateRole/') ?>" + id;



                }
            );



        });
</script>