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
$editable_attr = $this->getEditable();
$placeholder = $this->getPlaceholder();
$label = $this->getLabel();
$show_error_messages = $this->showErrors() && $field !== NULL;

if(!empty($label))
{
	?><label for="<?php echo $id; ?>" class="control-label"><?php echo $label; ?></label> <?php
}

$viewable = $field === NULL || $this->rights->isAttributeView($field);

if($viewable)
{
	$parent_form = $this->getParent('form');
	$editable = $field === NULL || ($field->isEditable() && $this->rights->isAttributeEdit($field) && $editable_attr);
	if(!$editable || $parent_form === NULL)
	{
		$content = $value;
		if($type == 'textarea')
		{
			$content = nl2br($value);
		}
		else
		if($value instanceof \Biome\Core\ORM\Models)
		{
			$value = $value->getId();
		}

		?><p class="form-control-static"><input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/><?php echo $content; ?></p><?php
	}
	else
	{
		switch($type)
		{
			case 'textarea':
				?><textarea id="<?php echo $id; ?>" class="<?php echo $classes; ?>" name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" aria-describedby="<?php echo $id; ?>_help"><?php echo $value; ?></textarea><?php
				break;
			case 'enum':
				?><select id="<?php echo $id; ?>" class="<?php echo $classes; ?>" name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" aria-describedby="<?php echo $id; ?>_help"><?php

					if(!$this->getField()->isRequired())
					{
						echo '<option value=""></option>';
					}

					foreach($this->getField()->getEnumeration() AS $key => $value)
					{
						echo '<option value="', $key, '">', $value, '</option>';
					}

				?></select><?php
				break;
			case 'selector':
			case 'many2one':
				?><select id="<?php echo $id; ?>" class="<?php echo $classes; ?>" name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" aria-describedby="<?php echo $id; ?>_help"><?php

					if($type == 'many2one')
					{
						//$object = $this->getParentValue();
						$object = $field->getObjectName();
					}
					else
					{
						$object = $this->getAttribute('object');
					}

					$var = $this->getAttribute('var');

					$query_set = $object::all();
					foreach($query_set AS $o)
					{
						?><option value="<?php echo $o->getId(); ?>"><?php

						$this->setContext($var, $o);
						echo $this->getContent();
						$this->unsetContext($var);

						?></option><?php
					}

				?></select><?php
				$this->view->javascript(function() use($id) {
					?>
					$('#<?php echo $id; ?>').select2();
					<?php
				});

				break;
			default:
				?><input id="<?php echo $id; ?>" class="<?php echo $classes; ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>" aria-describedby="<?php echo $id; ?>_help"/><?php
		}
	}
}
else
{
	?><p class="form-control-static"><i class="fa fa-ban"></i></p><?php
}

if($show_error_messages)
{
	$errors = $field->getErrors();
	if(!empty($errors))
	{
		?><span id="<?php echo $id; ?>_help" class="help-block"><?php echo join('<br/>', $errors); ?></span><?php
	}
}
?></div> <?php
