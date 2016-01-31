<?php

namespace Biome\Core\ORM\Inspector;

use Biome\Core\ORM\AbstractField;
use Biome\Core\ORM\RawSQL;

class SQLModelInspector implements ModelInspectorInterface
{
	protected $database = '';
	protected $table = '';
	protected $primary_keys = array();

	protected $fields = array();

	public function handleParameters(array $parameters)
	{
		$this->database	= !empty($parameters['database']) ? $parameters['database'] : NULL;
		$this->table	= $parameters['table'];

		$this->primary_keys	=  is_array($parameters['primary_key']) ? $parameters['primary_key'] : array($parameters['primary_key']);
	}

	public function handleField(AbstractField $field)
	{
		$name			= $field->getName();
		$defaultValue	= $field->getDefaultValue();
		$required		= $field->isRequired();

		$default = '';
		if($required)
		{
			$default = 'NOT NULL';
			if($defaultValue)
			{
				if($defaultValue instanceof RawSQL)
				{
					$default .= ' DEFAULT ' . $defaultValue->get() . '';
				}
				else
				{
					$default .= ' DEFAULT "' . $defaultValue . '"';
				}
			}
		}
		else
		{
			if($defaultValue instanceof RawSQL)
			{
				$default = $defaultValue->get();
			}
			else
			{
				$default = 'DEFAULT NULL';
			}
		}

		if(in_array($name, $this->primary_keys))
		{
			$default = 'NOT NULL';
		}

		switch($field->getType())
		{
			case 'primary':
				$type = 'INT(10) unsigned';
				$default = 'NOT NULL';
				if($field->getAutoId())
				{
					$default .= ' AUTO_INCREMENT';
				}
				break;
			case 'text':
			case 'password':
			case 'email':
				$type = 'VARCHAR(' . $field->getSize() . ')';
				break;
			case 'enum':
				$type = 'VARCHAR(32)';
				break;
			case 'datetime':
				$type = 'TIMESTAMP';
				break;
			case 'textarea':
				$type = 'TEXT';
				break;
			case 'boolean':
				$type = 'TINYINT(1)';
				break;
			case 'int':
				$type = 'INT';
				break;
			case 'float':
				$type = 'DOUBLE';
				break;
			case 'many2one':
				$type = 'INT(10) unsigned';

				if(substr($name, -3) !== '_id')
				{
					return FALSE;
				}
				break;
			default:
				return FALSE;
		}

		$this->fields[$name] = '`' . $name . '` ' . $type . ' ' . $default;

		return TRUE;
	}

	public function generate()
	{
		$query = 'CREATE TABLE `' . $this->table . '` (' . PHP_EOL;
		$query .= join(',' . PHP_EOL, $this->fields);

		if(!empty($this->primary_keys))
		{
			$query .= ',' . PHP_EOL . 'PRIMARY KEY(`' . join('`, `', $this->primary_keys) . '`)';
		}
		$query .=  PHP_EOL;
		$query .= ');' . PHP_EOL;

		return $query;
	}
}
