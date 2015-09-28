<div class="form-group"><?php

$id = $this->getId();
$type = $this->getType();
$classes = $this->getClasses();
$name = $this->getName();
$value = $this->getValue();
$placeholder = $this->getPlaceholder();
$label = $this->getLabel();

switch($type)
{
	case 'password':
		$value = '';
		break;
	default:

}
if(!empty($label))
{
	?><label for="<?php echo $id; ?>"><?php echo $label; ?></label><?php
}

?><input id="<?php echo $id; ?>" class="<?php echo $classes; ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>"/></div>  <?php
