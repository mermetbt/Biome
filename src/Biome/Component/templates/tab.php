<?php

$pages_list	= $this->getChildren('page');

?><div id="<?php echo $this->getId(); ?>"><?php

?><ul class="nav nav-tabs" role="tablist"><?php

$first = true;
foreach($pages_list AS $page)
{
	$title = $page->getTitle();
	$page_id = $page->getId();

	if($first)
	{
		$first = false;
		$page->addClasses('in active');
	}

	?><li role="presentation" class="active"><a href="#<?php echo $page_id; ?>" aria-controls="<?php echo $page_id; ?>" role="tab" data-toggle="tab"><?php echo $title; ?></a></li><?php
}

?></ul><?php
?><div class="tab-content"><?php

	echo $this->getContent();

?></div><?php
?></div><?php
