<div class="mb-3">


<label class="form-label">
<?=$label;?>
</label>


<select name="<?=$name;?>"
class="form-control">


<option value="">
Select
</option>


<?php foreach($options as $option): ?>


<option value="<?= $option->id ?>">
    <?= html_escape($option->value) ?>
</option>


<?php endforeach;?>


</select>


<?=form_error($name,'<small class="text-danger">','</small>');?>


</div>