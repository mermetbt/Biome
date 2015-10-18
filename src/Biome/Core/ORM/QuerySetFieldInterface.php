<?php

namespace Biome\Core\ORM;

interface QuerySetFieldInterface
{
	public function generateQuerySet(QuerySet $query_set, $field_name);
}
