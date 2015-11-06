<?php
$field = $this->getField();

$class = 'form-group';
if($field !== NULL && $field->hasErrors())
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
$show_error_messages = $this->showErrors() && $field !== NULL;

if(!empty($label))
{
	?><label for="<?php echo $id; ?>" class="control-label"><?php echo $label; ?></label> <?php
}

$rights = \Biome\Biome::getService('rights');

$viewable = $field === NULL || $rights->isAttributeView($field);

if($viewable)
{
	$parent_form = $this->getParent('form');
	$editable = $field === NULL || ($field->isEditable() && $rights->isAttributeEdit($field));

	if(!$editable || $parent_form === NULL)
	{
		if($type == 'textarea')
		{
			$value = nl2br($value);
		}

		?><p class="form-control-static"><?php echo $value; ?></p><?php
	}
	else
	{
		switch($type)
		{
			case 'textarea':
				?><textarea id="<?php echo $id; ?>" class="<?php echo $classes; ?>" name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" aria-describedby="<?php echo $id; ?>_help"><?php echo $value; ?></textarea><?php
				break;
			default:
						?><input id="<?php echo $id; ?>" class="<?php echo $classes; ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>" aria-describedby="<?php echo $id; ?>_help"/><?php
		}
	}
}
else
{
	?><i class="fa fa-ban"></i><?php
}

if($show_error_messages)
{
	$errors = $field->getErrors();
	if(!empty($errors))
	{
		?><span id="<?php echo $id; ?>_help" class="help-block"><?php echo join('<br/>', $errors); ?></span><?php
	}
}
?></div>  <?php
