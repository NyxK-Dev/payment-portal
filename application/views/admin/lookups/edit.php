<div class="container-fluid">


    <!-- Validation Error -->
    <?php if (validation_errors()): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?= validation_errors(); ?>

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <div class="card card-primary card-outline">


        <div class="card-header">

            <h5 class="card-title">
                <i class="bi bi-pencil-square me-2"></i>
                Update Lookup Information
            </h5>

        </div>



        <form method="post"
            action="<?= site_url('admin/lookups/update/' . $lookup->id); ?>">


            <!-- CSRF -->
            <input type="hidden"
                name="<?= $this->security->get_csrf_token_name(); ?>"
                value="<?= $this->security->get_csrf_hash(); ?>">



            <div class="card-body">


                <div class="row">


                    <!-- Code -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Code
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-code-square"></i>
                            </span>

                            <input type="text"
                                name="code"
                                class="form-control"
                                placeholder="Enter code"
                                value="<?= set_value('code', $lookup->code); ?>">

                        </div>

                    </div>



                    <!-- Value -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Value
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-tag"></i>
                            </span>

                            <input type="text"
                                name="value"
                                class="form-control"
                                placeholder="Enter value"
                                value="<?= set_value('value', $lookup->value); ?>">

                        </div>

                    </div>




                    <!-- Description -->
                    <div class="col-md-12 mb-3">

                        <label class="form-label fw-bold">
                            Description
                        </label>


                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                            placeholder="Enter description"><?= set_value('description', $lookup->description); ?></textarea>


                    </div>




                    <!-- Sort Order -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Sort Order
                        </label>


                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-sort-numeric-down"></i>
                            </span>


                            <input type="number"
                                name="sort_order"
                                class="form-control"
                                value="<?= set_value('sort_order', $lookup->sort_order); ?>">

                        </div>


                    </div>





                    <!-- Status -->
                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">
                            Status
                        </label>


                        <select name="is_active"
                            class="form-select">


                            <option value="1"
                                <?= ($lookup->is_active == 1) ? 'selected' : ''; ?>>
                                Active
                            </option>


                            <option value="0"
                                <?= ($lookup->is_active == 0) ? 'selected' : ''; ?>>
                                Inactive
                            </option>


                        </select>


                    </div>


                </div>


            </div>




            <!-- Footer Buttons -->
            <div class="card-footer text-end">


                <a href="<?= site_url('admin/lookups'); ?>"
                    class="btn btn-secondary me-2">

                    <i class="bi bi-arrow-left"></i>
                    Cancel

                </a>



                <button type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-check-circle"></i>
                    Update

                </button>


            </div>



        </form>


    </div>


</div>