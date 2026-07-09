<div class="card">

<div class="card-header">
    <h5>Create Product</h5>
</div>


<div class="card-body">


<form method="post"
      action="<?= site_url('admin/products/store'); ?>">


<input
    type="hidden"
    name="<?= $this->security->get_csrf_token_name(); ?>"
    value="<?= $this->security->get_csrf_hash(); ?>"
>


<div class="row">


<div class="col-md-6">

<?php
$this->load->view(
    'components/form_input',
    [
        'name'=>'name',
        'label'=>'Product Name',
        'value'=>set_value('name')
    ]
);
?>


</div>



<div class="col-md-6">

<?php
$this->load->view(
    'components/form_input',
    [
        'name'=>'sku',
        'label'=>'SKU',
        'value'=>set_value('sku')
    ]
);
?>


</div>



<div class="col-md-6">

<?php
$this->load->view(
    'components/form_select',
    [
        'name'=>'category_lookup_id',
        'label'=>'Category',
        'options'=>$categories
    ]
);
?>


</div>



<div class="col-md-6">


<?php
$this->load->view(
    'components/form_select',
    [
        'name'=>'status_lookup_id',
        'label'=>'Status',
        'options'=>$statuses
    ]
);
?>


</div>



<div class="col-md-6">

<?php
$this->load->view(
    'components/form_input',
    [
        'name'=>'price',
        'label'=>'Price',
        'type'=>'number'
    ]
);
?>


</div>



<div class="col-md-6">

<?php
$this->load->view(
    'components/form_input',
    [
        'name'=>'stock_qty',
        'label'=>'Stock Quantity',
        'type'=>'number'
    ]
);
?>


</div>



<div class="col-md-12">

<label>
Description
</label>

<textarea name="description"
          class="form-control">

<?= set_value('description'); ?>

</textarea>


</div>


</div>



<br>


<button class="btn btn-primary">
Save Product
</button>


<a href="<?=site_url('admin/products')?>"
   class="btn btn-secondary">

Cancel

</a>



</form>


</div>

</div>