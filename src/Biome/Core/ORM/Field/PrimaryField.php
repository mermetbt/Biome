<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;

class PrimaryField extends AbstractField
{
	protected $required = FALSE;
	protected $auto_id = TRUE;

	public function getAutoId()
	{
		return $this->auto_id;
	}

	public function setAutoId($auto_id = TRUE)
	{
		$this->auto_id = $auto_id == TRUE;
		return $this;
	}
}
