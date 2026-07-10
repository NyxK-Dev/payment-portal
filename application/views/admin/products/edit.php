<div class="card">

<div class="card-header">
    <h5>Edit Product</h5>
</div>


<div class="card-body">


<form method="post"
      action="<?=site_url('admin/products/update/'.$product->id);?>">


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
                'value'=>$product->name
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
                'value'=>$product->sku
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
                'type'=>'number',
                'value'=>$product->price
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
                'type'=>'number',
                'value'=>$product->stock_qty
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
                'options'=>$categories,
                'selected'=>$product->category_lookup_id
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
                'options'=>$statuses,
                'selected'=>$product->status_lookup_id
            ]
        );
        ?>

    </div>



    <div class="col-md-12">

        <label>Description</label>

        <textarea 
            name="description"
            class="form-control"
            rows="4"><?= $product->description ?></textarea>

    </div>


</div>


<br>


<button class="btn btn-success">
    Update
</button>


<a href="<?=site_url('admin/products')?>"
   class="btn btn-secondary">

    Cancel

</a>


</form>


</div>

</div>