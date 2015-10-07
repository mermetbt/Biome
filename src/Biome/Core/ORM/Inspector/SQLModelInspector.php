<?php

namespace Biome\Core\ORM\Inspector;

use Biome\Core\ORM\AbstractField;

class SQLModelInspector implements ModelInspectorInterface
{
	protected $database = '';
	protected $table = '';
	protected $primary_keys = array();

	protected $fields = array();

	public function handleParameters(array $parameters)
	{
		$this->database	= $parameters['database'];
		$this->table	= $parameters['table'];

		if(is_array($parameters['primary_key']))
		{
			$this->primary_keys	= $parameters['primary_key'];
		}
		else
		{
			$this->primary_keys	= array($parameters['primary_key']);
		}
	}

	public function handleField(AbstractField $field)
	{
		$name = $field->getName();
		$defaultValue = $field->getDefaultValue();
		$required = $field->isRequired();

		$default = '';
		if($required)
		{
			$default = 'NOT NULL';
			if($defaultValue)
			{
				$default .= ' DEFAULT "' . $defaultValue . '"';
			}
		}
		else
		{
			$default = 'DEFAULT NULL';
		}

		switch($field->getType())
		{
			case 'primary':
				$type = 'INT(10) unsigned';
				$default = 'NOT NULL AUTO_INCREMENT';
				break;
			case 'text':
			case 'password':
			case 'email':
				$type = 'VARCHAR(' . $field->getSize() . ')';
				break;
			case 'int':
				$type = 'INT';
				break;
			case 'float':
				$type = 'DOUBLE';
				break;
			default:
				$type = '';
		}

		$this->fields[$name] = '`' . $name . '` ' . $type . ' ' . $default;
	}

	public function generate()
	{
		$query = 'CREATE TABLE `' . $this->table . '` (' . PHP_EOL;
		$query .= join(',' . PHP_EOL, $this->fields) . PHP_EOL;
		$query .= ');' . PHP_EOL;

		$query .= 'ALTER TABLE `' . $this->table . '` ADD PRIMARY KEY (' . join(', ', $this->primary_keys) . ');' . PHP_EOL;

		return $query;
	}
}
