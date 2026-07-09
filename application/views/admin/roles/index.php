<div class="container mt-5">


    <div class="d-flex justify-content-between mb-4">


        <a href="<?= site_url('admin/roles/create'); ?>"
            class="btn btn-primary ms-auto">

            Add Role

        </a>


    </div>



    <div class="card shadow-sm">

        <div class="card-body">


            <table class="table table-bordered">


                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Name</th>
                        <th>Action</th>

                    </tr>

                </thead>



                <tbody>


                    <?php if (!empty($roles)): ?>

                        <?php foreach ($roles as $role): ?>


                            <tr>


                                <td>
                                    <?= $role->id ?>
                                </td>



                                <td>

                                    <span class="badge bg-info">

                                        <?= ucfirst($role->name) ?>

                                    </span>

                                </td>



                                <td>


                                    <a href="<?= site_url('admin/roles/edit/' . $role->id); ?>"
                                        class="btn btn-warning btn-sm">

                                        Edit

                                    </a>



                                    <a href="<?= site_url('admin/roles/delete/' . $role->id); ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this role?');">

                                        Delete

                                    </a>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <tr>

                            <td colspan="3" class="text-center">

                                No roles found.

                            </td>

                        </tr>


                    <?php endif; ?>


                </tbody>


            </table>


        </div>

    </div>


</div>