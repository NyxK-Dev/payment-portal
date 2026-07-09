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

<input type="hidden"
name="id"
value="<?=$product->id;?>">



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



<?php

$this->load->view(
'components/form_input',
[
'name'=>'stock_qty',
'label'=>'Stock',
'type'=>'number',
'value'=>$product->stock_qty
]
);

?>



<label>Description</label>


<textarea name="description"
class="form-control">

<?=$product->description?>

</textarea>



<br>


<button class="btn btn-success">
Update
</button>



</form>


</div>

</div>