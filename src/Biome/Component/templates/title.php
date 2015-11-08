<?php

$content = $this->getContent();

if(!empty($content))
{
	echo '<div><div class="pull-right">', $content, '</div>';
}

echo '<div><h1>', $this->getTitle(), '</h1><hr/></div>';

if(!empty($content))
{
	echo '</div>';
}

