<?php
$currentValue = set_value(
    $name,
    isset($selected) ? $selected : ''
);
?>

<?php if (!empty($label)): ?>
    <label class="form-label">
        <?= html_escape($label); ?>
    </label>
<?php endif; ?>

<select name="<?= $name; ?>" class="form-control mb-3">

    <option value="">Select <?= html_escape($label ?? 'Option'); ?></option>

    <?php foreach ($options as $option): ?>
        <option
            value="<?= $option->id; ?>"
            <?= $currentValue == $option->id ? 'selected' : ''; ?>
        >
            <?= html_escape($option->value); ?>
        </option>
    <?php endforeach; ?>

</select>

<?php if (form_error($name)): ?>
    <small class="text-danger">
        <?= form_error($name); ?>
    </small>
<?php endif; ?>