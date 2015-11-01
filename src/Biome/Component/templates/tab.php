<?php

$pages_list	= $this->getChildren('page');

?><div id="<?php echo $this->getId(); ?>"><?php

?><ul class="nav nav-tabs" role="tablist"><?php

$first = true;
foreach($pages_list AS $page)
{
	$title = $page->getTitle();
	$page_id = $page->getId();

	?><li role="presentation" class="<?php echo $first ? 'active' : ''; ?>"><a href="#<?php echo $page_id; ?>" aria-controls="<?php echo $page_id; ?>" role="tab" data-toggle="tab"><?php echo $title; ?></a></li><?php

	if($first)
	{
		$first = false;
		$page->addClasses('in active');
	}
}

?></ul><?php
?><div class="tab-content"><?php

	echo $this->getContent();

?></div><?php
?></div><?php
