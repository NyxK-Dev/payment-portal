<div class="container-fluid">


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

                <i class="bi bi-plus-circle me-2"></i>

                Create Lookup Value

            </h5>

        </div>




        <form method="post"
            action="<?= site_url('admin/lookups/store/' . $group->id); ?>">



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
                                placeholder="Enter code">


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
                                placeholder="Enter value">


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
                            placeholder="Enter description"></textarea>


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
                                value="0"
                                class="form-control">


                        </div>


                    </div>







                    <!-- Status -->
                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">
                            Status
                        </label>



                        <select name="is_active"
                            class="form-select">


                            <option value="1">
                                Active
                            </option>


                            <option value="0">
                                Inactive
                            </option>


                        </select>


                    </div>



                </div>


            </div>






            <div class="card-footer text-end">


                <a href="<?= site_url('admin/lookups/' . $group->id); ?>"
                    class="btn btn-secondary me-2">


                    <i class="bi bi-arrow-left"></i>

                    Cancel


                </a>





                <button type="submit"
                    class="btn btn-primary">


                    <i class="bi bi-check-circle"></i>

                    Save


                </button>



            </div>





        </form>


    </div>


</div>