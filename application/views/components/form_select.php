<?php
$currentValue = set_value(
    $name,
    isset($selected) ? $selected : ''
);
?>

<select name="<?= $name; ?>" class="form-control">

    <option value="">Select</option>

    <?php foreach ($options as $option): ?>
        <option
            value="<?= $option->id; ?>"
            <?= $currentValue == $option->id ? 'selected' : ''; ?>
        >
            <?= html_escape($option->value); ?>
        </option>
    <?php endforeach; ?>

</select>