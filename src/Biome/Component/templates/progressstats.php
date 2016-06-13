<?php

$value = round($this->getValue());

$title = $this->getTitle();
$description = $this->getDescription();

$type = $this->getType();

echo '<div class="row progress-stats"><div class="col-lg-8">';

if(!empty($title))
{
	echo '<h5 class="name">', $title, '</h5>';
}

if(!empty($description))
{
	echo '<p class="description">', $description, '</p>';
}

echo '<div class="progress progress-sm"><div class="progress-bar active progress-bar-striped progress-bar-', $type,'" role="progressbar" aria-valuenow="', $value, '" aria-valuemin="0" aria-valuemax="100" style="width: ', $value, '%;"><span class="sr-only">', $value, '% Complete</span></div></div>';
echo '</div>';

echo '<div class="col-lg-4"><div class="percentage">', $value, '%</div></div>';

echo '</div>';
