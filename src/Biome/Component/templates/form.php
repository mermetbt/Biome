<form class="<?php echo $this->getClasses(); ?>" method="POST" action="<?php echo $this->getAction() ?>">
<input type="hidden" name="_token" value="<?php echo \Biome\Biome::getService('view')->getViewState(); ?>"/>
<?php echo $this->getContent(); ?>
</form>
