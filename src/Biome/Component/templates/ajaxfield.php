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
	?><label for="<?php echo $id; ?>" class="control-label col-sm-3"><?php echo $label; ?></label> <?php
}

?><div id="<?php echo $id; ?>"<?php
?> data-type="<?php echo $type; ?>" data-url="<?php echo $url; ?>" data-name="<?php echo $name; ?>"<?php

if($type == 'many2one')
{
	if($value instanceof \Biome\Core\ORM\Models)
	{
		$data_id = $value->getId();
		$value = (string) $value;
	}
	else
	{
		$data_id = $value;
		$last = NULL;
		$object = $this->getObject($last);

		if(substr($last, -3) == '_id')
		{
			$last = substr($last, 0, -3);
			$value = (string)$object->$last;
		}
	}
	?> data-id="<?php echo $data_id; ?>"<?php
}

if($type == 'boolean' || $type == 'enum')
{
	$array_id = 'options_' . uniqid();
	?> data-options="<?php echo $array_id; ?>" data-value="<?php echo $value; ?>"<?php

	if($type == 'boolean')
	{
		$options = array('1' => 'Yes', '0' => 'No');
	}
	else
	{
		$options = $this->getField()->getEnumeration();
	}

	if(isset($options[$value]))
	{
		$value = $options[$value];
	}
	else
	{
		$value = '';
	}

$this->view->javascript(function() use($array_id, $options) {
?>
var <?php echo $array_id; ?> = <?php echo json_encode($options); ?>;
<?php
});
}

?> class="ajaxfield col-sm-9 form-group"><?php

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
