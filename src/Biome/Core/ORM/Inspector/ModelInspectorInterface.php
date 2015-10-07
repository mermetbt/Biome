<?php

namespace Biome\Core\ORM\Inspector;

use Biome\Core\ORM\AbstractField;

interface ModelInspectorInterface
{
	public function handleParameters(array $parameters);

	public function handleField(AbstractField $field);
}
