<?php if (!empty($group)): ?>
    <div class="mb-3">
        <a href="<?= site_url('admin/lookupgroups'); ?>"
            class="text-primary text-decoration-none small d-inline-flex align-items-center link-primary">
            <i class="fas fa-arrow-left me-1"></i>
            <span>Back to Lookup Groups</span>
        </a>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="text-muted small">
        Manage application dropdowns, categories, and system constants.
    </div>

    <?php if (!empty($group)): ?>
        <a href="<?= site_url('admin/lookups/create/' . $group->id); ?>"
            class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="bi bi-plus-circle me-1"></i>
            Add New Value
        </a>
    <?php endif; ?>
</div>

<div class="card card-outline card-primary">


    <div class="card-body p-0">


        <div class="table-responsive">


            <table class="table table-hover align-middle mb-0">


                <thead class="table-light">


                    <tr>

                        <th width="80">
                            ID
                        </th>


                        <th>
                            Group / Category
                        </th>


                        <th>
                            Code
                        </th>


                        <th>
                            Value Name
                        </th>


                        <th>
                            Description
                        </th>


                        <th width="140"
                            class="text-end">
                            Actions
                        </th>


                    </tr>


                </thead>



                <tbody>


                    <?php if (!empty($lookups)): ?>


                        <?php foreach ($lookups as $item): ?>


                            <tr>


                                <td class="fw-bold text-secondary">

                                    #<?= $item->id; ?>

                                </td>




                                <td>


                                    <span class="badge rounded-pill bg-light text-secondary border">


                                        <?= htmlspecialchars($item->group_name ?? 'Unassigned'); ?>


                                    </span>


                                </td>




                                <td>


                                    <code>

                                        <?= htmlspecialchars($item->code); ?>

                                    </code>


                                </td>




                                <td class="fw-bold">


                                    <?= htmlspecialchars($item->value); ?>


                                </td>




                                <td>


                                    <span class="text-muted small">


                                        <?= htmlspecialchars($item->description ?? '—'); ?>


                                    </span>


                                </td>





                                <td class="text-end text-nowrap">


                                    <a href="<?= site_url('admin/lookups/edit/' . $item->id); ?>"
                                        class="btn btn-sm btn-outline-primary me-1">

                                        <i class="bi bi-pencil"></i>
                                        Edit

                                    </a>



                                    <a href="<?= site_url('admin/lookups/delete/' . $item->id); ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to delete this lookup value?');">

                                        <i class="bi bi-trash"></i>
                                        Delete

                                    </a>


                                </td>


                            </tr>


                        <?php endforeach; ?>



                    <?php else: ?>


                        <tr>


                            <td colspan="6"
                                class="text-center py-5 text-muted">


                                <i class="bi bi-inbox fs-2"></i>


                                <p class="fw-bold mt-2 mb-1">

                                    No configurations found

                                </p>


                                <p class="small mb-0">

                                    Click "Add New Value" to create lookup records.

                                </p>


                            </td>


                        </tr>



                    <?php endif; ?>


                </tbody>



            </table>


        </div>


    </div>


</div>