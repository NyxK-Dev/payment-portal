<div class="card">


    <div class="card-header">

        <h3 class="card-title">
            Lookup Groups
        </h3>


        <div class="card-tools">

            <a href="<?= base_url('admin/lookupgroups/create'); ?>"
               class="btn btn-primary btn-sm">

                Add Lookup Group

            </a>

        </div>

    </div>



    <div class="card-body">


        <table class="table table-bordered table-striped">


            <thead>

                <tr>

                    <th>ID</th>

                    <th>Code</th>

                    <th>Name</th>

                    <th>Description</th>

                    <th>Created</th>

                    <th>Action</th>

                </tr>

            </thead>



            <tbody>


            <?php foreach($lookupgroups as $item): ?>


                <tr>

                    <td>
                        <?= $item->id; ?>
                    </td>


                    <td>
                        <?= html_escape($item->code); ?>
                    </td>


                    <td>
                        <?= html_escape($item->name); ?>
                    </td>


                    <td>
                        <?= html_escape($item->description); ?>
                    </td>


                    <td>
                        <?= $item->created_at; ?>
                    </td>


                    <td>


                        <a href="<?= base_url('admin/lookupgroups/edit/'.$item->id); ?>"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>


                    </td>


                </tr>


            <?php endforeach; ?>


            </tbody>


        </table>


    </div>


</div>