<?php

$action = $this->getAction();

$rights = \Biome\Biome::getService('rights');

if($rights->isUrlAllowed('POST', $action))
{
	?><button id="<?php echo $this->getId(); ?>" class="<?php echo $this->getClasses(); ?>" type="submit" onclick="$(this).closest('form').attr('action', '<?php echo $action; ?>');$(this).closest('form').submit();"><?php echo $this->getValue(); ?></button><?php
}
