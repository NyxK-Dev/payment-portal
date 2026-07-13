<div class="container mt-5">

    <div class="card shadow-sm">


        <div class="card-body">


            <div class="row mb-3">

                <div class="col-md-4 ms-auto">
                    <div class="input-group">

                        <input
                            type="text"
                            id="searchUser"
                            class="form-control"
                            placeholder="Search by ID, Name, Email, Role or Status">

                        <button
                            type="button"
                            id="resetSearch"
                            class="btn btn-secondary">
                            <i class="fa fa-sync"></i>
                        </button>

                    </div>
                </div>

            </div>
            <table class="table table-bordered" id="userTable">


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

    const searchInput = document.getElementById('searchUser');
    const resetButton = document.getElementById('resetSearch');

    function filterUsers() {

        let value = searchInput.value.toLowerCase();

        let rows = document.querySelectorAll('#userTable tbody tr');

        rows.forEach(function(row) {

            let text = row.textContent.toLowerCase();

            if (text.indexOf(value) > -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }

        });

    }


    // Search
    searchInput.addEventListener('keyup', function() {

        filterUsers();

    });


    // Reset
    resetButton.addEventListener('click', function() {

        searchInput.value = '';

        filterUsers();

    });
</script>