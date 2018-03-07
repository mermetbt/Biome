<?php

use Biome\Core\Command\AbstractCommand;

class ObjectCommand extends AbstractCommand
{
	protected function plural($object)
	{
		if($object[(strlen($object)-1)] == 's')
		{
			$objects = $object . 'es';
		}
		else
		if($object[(strlen($object)-1)] == 'y')
		{
			$objects = substr($object, 0, -1) . 'ies';
		}
		else
		{
			$objects = $object . 's';
		}
		return $objects;
	}

	protected function createModel($object)
	{
		$tablename = strtolower($this->plural($object));

		$primary_key = strtolower($object) . '_id';
		$object_name = strtolower($object) . '_name';

		$filename = APP_DIR . '/app/models/' . $object . '.php';

		if(file_exists($filename))
		{
			return FALSE;
		}

		$content = <<<EOF
<?php

use Biome\Core\ORM\Models;

use Biome\Core\ORM\Field\PrimaryField;
use Biome\Core\ORM\Field\TextField;
use Biome\Core\ORM\Field\DateTimeField;

use Biome\Core\ORM\RawSQL;

class $object extends Models
{
	public function parameters()
	{
		return array(
					'table'			=> '$tablename',
					'primary_key'	=> '$primary_key',
					'reference'		=> '$object_name'
		);
	}

	public function fields()
	{
		\$this->$primary_key				= PrimaryField::create()
										->setLabel('@string/$primary_key');

		\$this->$object_name				= TextField::create()
										->setLabel('@string/$object_name');

		\$this->c_date				= DateTimeField::create()
										->setLabel('@string/creation_date')
										->setRequired(TRUE)
										->setEditable(FALSE)
										->setDefaultValue(RawSQL::select('CURRENT_TIMESTAMP'));

		\$this->m_date				= DateTimeField::create()
										->setLabel('@string/modification_date')
										->setEditable(FALSE)
										->setDefaultValue(RawSQL::select('ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT NULL'));
	}
}
EOF;

		return file_put_contents($filename, $content);
	}

	protected function createController($object)
	{
		$objects = $this->plural($object);
		$filename = APP_DIR . '/app/controllers/' . $object . 'Controller.php';

		if(file_exists($filename))
		{
			return FALSE;
		}

		$content = <<<EOF
<?php

use Biome\Core\Controller\ObjectControllerTrait;

class ${object}Controller extends BaseController
{
	use ObjectControllerTrait;

	public function objectName()
	{
		return '$object';
	}

	public function collectionName()
	{
		return '$objects';
	}
}
EOF;
		return file_put_contents($filename, $content);
	}

	protected function createCollection($object)
	{
		$objects = $this->plural($object);
		$object_lower = strtolower($object);
		$objects_lower = strtolower($objects);

		$filename = APP_DIR . '/app/collections/' . $objects . 'Collection.php';

		if(file_exists($filename))
		{
			return FALSE;
		}

		$content = <<<EOF
<?php

use Biome\Core\Collection\RequestCollection;

class ${objects}Collection extends RequestCollection
{
	protected \$map = array(
		'$object_lower' => '$object',
		'$objects_lower' => array()
	);

	public function get${objects}()
	{
		if(empty(\$this->$objects_lower))
		{
			\$this->$objects_lower = $object::all();
		}
		return \$this->$objects_lower;
	}
}
EOF;
		return file_put_contents($filename, $content);
	}

	protected function createView($object)
	{
		$objects = $this->plural($object);
		$object_lower = strtolower($object);
		$objects_lower = strtolower($objects);

		$primary_key = strtolower($object) . '_id';
		$object_name = strtolower($object) . '_name';

		$filename = APP_DIR . '/app/views/' . $object_lower . '.xml';

		if(file_exists($filename))
		{
			return FALSE;
		}

		$content = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<biome:views xmlns:biome="http://github.com/mermetbt/Biome/">

	<biome:include src="elements/navbar.xml"/>

	<biome:view action="index">
		<div class="container-fluid">
			<biome:title title="@string/{$objects_lower}_title">
					<biome:a class="btn btn-sm btn-success" controller="$object_lower" action="create"><i class="fa fa-plus"></i> <biome:text value="@string/new"/></biome:a>
			</biome:title>

			<biome:panel title="@string/{$objects_lower}_list_title">
				<biome:datatable value="#{{$objects_lower}.{$objects_lower}}" var="$object_lower">
					<biome:column headerTitle="#">
						<biome:variable value="#{{$object_lower}.{$primary_key}}"/>
					</biome:column>

					<biome:column headerTitle="@string/$object_lower">
						<biome:a controller="$object_lower" action="show" item="#{{$object_lower}.{$primary_key}}">
							<biome:variable value="#{{$object_lower}.{$object_name}}"/>
						</biome:a>
					</biome:column>

					<biome:column headerTitle="@string/creation_date">
						<biome:variable value="#{{$object_lower}.c_date}"/>
					</biome:column>
				</biome:datatable>
			</biome:panel>
		</div>
	</biome:view>

	<biome:view action="show">
		<div class="container-fluid">
			<biome:title title="#{{$objects_lower}.{$object_lower}.{$object_name}}">
				<biome:a class="btn btn-sm btn-danger" controller="$object_lower" action="delete" item="#{{$objects_lower}.{$object_lower}.{$primary_key}}"><i class="fa fa-trash"></i> <biome:text value="@string/delete"/></biome:a>
			</biome:title>

			<div class="row">
				<div class="col-lg-12">
					<biome:panel title="@string/{$object_lower}_informations">
						<biome:ajaxfield value="#{{$objects_lower}.{$object_lower}.{$object_name}}"/>
						<biome:ajaxfield value="#{{$objects_lower}.{$object_lower}.c_date}"/>
						<biome:ajaxfield value="#{{$objects_lower}.{$object_lower}.m_date}"/>
					</biome:panel>
				</div>
			</div>
		</div>
	</biome:view>

	<biome:view action="create">
		<div class="container-fluid">
			<biome:title title="@string/{$objects_lower}_create_title"/>

			<biome:panel title="@string/{$object_lower}_informations">
				<biome:form>
					<biome:field value="#{{$object_lower}.{$object_name}}"/>
					<biome:button class="btn-success" value="@string/create" action="#{{$object_lower}.create}"/>
				</biome:form>
			</biome:panel>
		</div>
	</biome:view>

</biome:views>
EOF;
		return file_put_contents($filename, $content);
	}

	/**
	 * @description Create a new object with all the necessary files.
	 * @param object_name Name of the object
	 */
	public function create($object_name)
	{
		$object_name = ucfirst($object_name);

		/**
		 * Create the model.
		 */
		if($this->createModel($object_name))
		{
			echo 'Model generated for ', $object_name, PHP_EOL;
		}

		/**
		 * Create the controller.
		 */
		if($this->createController($object_name))
		{
			echo 'Controller generated for ', $object_name, PHP_EOL;
		}

		/**
		 * Create the collection.
		 */
		if($this->createCollection($object_name))
		{
			echo 'Collection generated for ', $object_name, PHP_EOL;
		}

		/**
		 * Create the view.
		 */
		if($this->createView($object_name))
		{
			echo 'View generated for ', $object_name, PHP_EOL;
		}
	}
}
