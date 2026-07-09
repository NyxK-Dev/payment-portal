<div class="container-fluid">

    <h3 class="mb-4">
        Create Lookup Group
    </h3>


    <?php if(validation_errors()): ?>

        <div class="alert alert-danger">
            <?= validation_errors(); ?>
        </div>

    <?php endif; ?>


    <form method="post"
          action="<?= base_url('admin/lookupgroups/store'); ?>">

        <input type="hidden"
               name="<?= $this->security->get_csrf_token_name(); ?>"
               value="<?= $this->security->get_csrf_hash(); ?>">

        <div class="mb-3">

            <label class="form-label">
                Code
            </label>

            <input 
                type="text"
                name="code"
                class="form-control"
                value="<?= set_value('code'); ?>"
            >

        </div>



        <div class="mb-3">

            <label class="form-label">
                Name
            </label>

            <input 
                type="text"
                name="name"
                class="form-control"
                value="<?= set_value('name'); ?>"
            >

        </div>



        <div class="mb-3">

            <label class="form-label">
                Description
            </label>


            <textarea
                name="description"
                class="form-control"
                rows="4"
            ><?= set_value('description'); ?></textarea>


        </div>



        <button class="btn btn-primary">
            Save
        </button>


    </form>


</div>