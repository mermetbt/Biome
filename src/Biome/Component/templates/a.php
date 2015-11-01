<?php

$allowed = $this->isAllowed();
if($allowed)
{
	echo '<a id="', $this->getId(), '" class="', $this->getClasses(), '" href="', $this->getURL(), '">';
}
echo $this->getContent();
if($allowed)
{
	echo '</a>';
}
