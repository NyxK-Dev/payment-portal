<div class="mb-3">

<label class="form-label">
<?=$label;?>
</label>


<input 
type="<?=isset($type)?$type:'text';?>"
name="<?=$name;?>"
value="<?=html_escape($value ?? '')?>"
class="form-control">


<?=form_error($name,'<small class="text-danger">','</small>');?>


</div>