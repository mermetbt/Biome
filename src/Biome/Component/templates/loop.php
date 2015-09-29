<?php

$var = $this->getVar();

foreach($this->getValue() AS $v)
{
	$this->setContext($var, $v);
	echo $this->getContent();
	$this->unsetContext($var);
}
