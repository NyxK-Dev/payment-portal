<div class="card">

<div class="card-header">

Product Detail

</div>


<div class="card-body">


<table class="table">


<tr>
<th>Name</th>
<td><?=$product->name?></td>
</tr>


<tr>
<th>SKU</th>
<td><?=$product->sku?></td>
</tr>


<tr>
<th>Price</th>
<td><?=$product->price?></td>
</tr>


<tr>
<th>Stock</th>
<td><?=$product->stock_qty?></td>
</tr>


<tr>
<th>Description</th>
<td><?=$product->description?></td>
</tr>


<tr>
<th>Created At</th>
<td><?=$product->created_at?></td>
</tr>


</table>



<a href="<?=site_url('admin/products')?>"
class="btn btn-secondary">

Back

</a>


</div>

</div>