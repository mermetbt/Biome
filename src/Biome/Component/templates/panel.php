<?php

$title = $this->getTitle();

?><div class="<?php echo $this->getClasses(); ?>"><?php

if(!empty($title))
{
	?><div class="panel-heading"><h3 class="panel-title"><?php echo $title; ?></h3></div><?php
}
?><div class="panel-body"><?php echo $this->getContent(); ?></div></div>
