<?php

use Biome\Core\ORM\Models;

use Biome\Core\ORM\Field\PrimaryField;
use Biome\Core\ORM\Field\TextField;
use Biome\Core\ORM\Field\DateTimeField;

use Biome\Core\ORM\RawSQL;

class Asset extends Models
{
	public function parameters()
	{
		return array(
					'table'			=> 'assets',
					'primary_key'	=> 'asset_id',
		);
	}

	public function fields()
	{
		$this->asset_id		= PrimaryField::create()
								->setLabel('@string/asset_id');

		$this->public_url	= TextField::create(255)
								->setLabel('@string/URL')
								->setRequired(TRUE);

		$this->creation_date	= DateTimeField::create()
									->setLabel('@string/creation_date')
									->setRequired(TRUE)
									->setEditable(FALSE)
									->setDefaultValue(RawSQL::select('CURRENT_TIMESTAMP'));

	}

	public function __toString()
	{
		return $this->public_url;
	}
}
