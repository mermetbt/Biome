<?php
$field = $this->getField();

$class = 'form-group';
if($field->hasErrors())
{
	$class .= ' has-error';
}

?><div class="<?php echo $class; ?>"><?php

$id = $this->getId();
$type = $this->getType();
$classes = $this->getClasses();
$name = $this->getName();
$value = $this->getValue();
$placeholder = $this->getPlaceholder();
$label = $this->getLabel();
$show_error_messages = $this->showErrors();

if(!empty($label))
{
	?><label for="<?php echo $id; ?>" class="control-label"><?php echo $label; ?></label> <?php
}

?><input id="<?php echo $id; ?>" class="<?php echo $classes; ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>" aria-describedby="<?php echo $id; ?>_help"/><?php

if($show_error_messages)
{
	$errors = $field->getErrors();
	if(!empty($errors))
	{
		?><span id="<?php echo $id; ?>_help" class="help-block"><?php echo join('<br/>', $errors); ?></span><?php
	}
}
?></div>  <?php
