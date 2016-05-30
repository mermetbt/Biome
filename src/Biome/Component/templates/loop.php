<?php

$var = $this->getVar();
$key = $this->getKeyName();
foreach($this->getValue() AS $k => $v)
{
	$this->setContext($var, $v);
	$this->setContext($key, $k);
	echo $this->getContent();
	$this->unsetContext($var);
	$this->unsetContext($key);
}
