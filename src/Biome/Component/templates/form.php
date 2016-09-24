<?php

$enctype = $this->getEnctype();
$type = '';
if(!empty($enctype))
{
	$type = ' enctype="' . $enctype . '"';
}

?><form class="<?php echo $this->getClasses(); ?>" method="POST" action="<?php echo $this->getAction() ?>"<?php echo $type; ?>>
<input type="hidden" name="_token" value="<?php echo \Biome\Biome::getService('view')->getViewState(); ?>"/>
<?php echo $this->getContent(); ?>
</form>
