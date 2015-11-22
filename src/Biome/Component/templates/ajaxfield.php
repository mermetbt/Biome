<?php

$field = $this->getField();

$id = $this->getId();
$type = $this->getType();
$classes = $this->getClasses();
$name = $this->getName();
$value = $this->getValue();
$placeholder = $this->getPlaceholder();
$label = $this->getLabel();

$url = URL::getUri() . '?partial=' . $id;

?><div class="form-horizontal"><?php
?><div class="form-group"><?php

if(!empty($label))
{
	?><label for="<?php echo $id; ?>" class="control-label col-sm-2"><?php echo $label; ?></label> <?php
}

?><div id="<?php echo $id; ?>" data-type="<?php echo $type; ?>" data-url="<?php echo $url; ?>" data-name="<?php echo $name; ?>" class="ajaxfield col-sm-10 form-group"><?php

$viewable = $field === NULL || $this->rights->isAttributeView($field);

$editable = $field === NULL || ($field->isEditable() && $this->rights->isAttributeEdit($field));

if($type == 'textarea')
{
	$value = nl2br($value);
}

if($viewable && $editable)
{
	?><p class="form-control-static"><a class="ajaxfield-input"><span class="content"><?php echo $value; ?></span> <i class="fa fa-pencil"></i></a></p><?php
}
else
if($viewable)
{
	?><p class="form-control-static"><?php echo $value; ?></p><?php
}
else
{
	?><p class="form-control-static"><i class="fa fa-ban"></i></p><?php
}

?></div><?php
?></div><?php
?></div><?php
