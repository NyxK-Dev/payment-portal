<div class="container mt-5">


    <div class="d-flex justify-content-between mb-4">

       

        <a href="<?= site_url('admin/permissions/create'); ?>"
            class="btn btn-primary ms-auto">

            Add Permission

        </a>


    </div>



    <div class="card shadow-sm">

        <div class="card-body">


            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach ($permissions as $permission): ?>


                        <tr>

                            <td>
                                <?= $permission->id ?>
                            </td>


                            <td>

                                <span class="badge bg-info">

                                    <?= $permission->code ?>

                                </span>

                            </td>


                            <td>
                                <?= $permission->name ?>
                            </td>


                            <td>


                                <a href="<?= site_url('admin/permissions/edit/' . $permission->id); ?>"
                                    class="btn btn-warning btn-sm">

                                    Edit

                                </a>


                                <a href="<?= site_url('admin/permissions/delete/' . $permission->id); ?>"
                                    class="btn btn-danger btn-sm">

                                    Delete

                                </a>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>


            </table>


        </div>

    </div>


</div>