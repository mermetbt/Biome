<?php

namespace Biome\Test;

use \TestObject1;

class ORMTestObjectTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateAndRetrieve()
	{
		$object = new TestObject1();

		$object->object_name = 'TestObject1';

		$this->assertEquals(5.0, $object->value);

		$this->assertTrue($object->save());

		$o = TestObject1::all()->filter('object_id', '=', $object->getId())->current();
		$this->assertNotEmpty($o);

		$o = new TestObject1();
		$o->object_name = 'First';
		$o->enumerate = 'first';
		$o->value = 2.0;
		$this->assertTrue($o->save());

		$o = new TestObject1();
		$o->object_name = 'Second';
		$o->enumerate = 'second';
		$o->value = 3.0;
		$this->assertTrue($o->save());

		$objects = TestObject1::all()
						->filter('value', '>', 1.0)
						->filter('value', '<', 4.0)
						->order_by('value ASC');

		$this->assertNotEmpty($objects);
		$first = $objects->current();
		$this->assertEquals('First', $first->object_name);
		$second = $objects->next();
		$this->assertEquals('Second', $second->object_name);

		$objects = TestObject1::all()
						->filter('value', 'in', [2.0, 3.0])
						->order_by('value DESC');

		$this->assertNotEmpty($objects);
		$first = $objects->current();
		$this->assertEquals('Second', $first->object_name);
		$second = $objects->next();
		$this->assertEquals('First', $second->object_name);
	}

}
