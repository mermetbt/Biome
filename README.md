# Biome PHP Framework

A simple framework for information systems application written in PHP.

## Getting started

### Welcome


#### What is Biome?

#### The goal of this framework

### Quick-start guide

	~ $ git clone https://github.com/mermetbt/Biome-application.git MyApp
	~ $ cd MyApp
	MyApp $ composer install

Set your environment variable

	~$ nano .env
	DB_HOSTNAME=localhost
	DB_USERNAME=root
	DB_PASSWORD=
	DB_DATABASE=myapp

### Directory structure

	- app
		- collections
		- commands
		- components
		- controllers
		- views
		- start.php
	- public
		- .htaccess
		- index.php
	- resources
		- css
		- fonts
		- images
		- js
		- less
	- storage
		- cache
		- logs
	- tests
		- src
		- bootstrap.php
	- .env.example
	- biome.php
	- composer.json
	- gulpfile.js
	- phpunit.xml



### Configuration

### CLI (Command Line Interface)

	MyApp $ ./biome.php

## Models

### Base structure

	class MyModel extends Models
	{
		public function parameters()
		{
			return array(
						'table'			=> 'my_model',
						'primary_key'	=> 'model_id'
			);
		}

		public function fields()
		{
			$this->model_id	= PrimaryField::create()
								->setLabel('@string/model_id');

			... Declaration of the fields
		}
	}

### Fields

### Object functions

## Controllers

	class MyModelController extends BaseController
	{

		public function getIndex()
		{

		}

		public function getShow($id)
		{

		}

		public function getCreate()
		{

		}
	}

## Views

### Templates

	<?xml version="1.0" encoding="UTF-8"?>
	<biome:views xmlns:biome="http://github.com/mermetbt/Biome/">

		<biome:view action="index">
			... Components shown when the action is GET /index or GET /
		</biome:view>

		<biome:view action="show">
			... Components shown when the action is GET /{id}/show with id the id of the object
		</biome:view>

		<biome:view action="create">
			... Components shown when the action is GET /create
		</biome:view>
	</biome:views>

### Components

## TODO

* Fix the SQL generation of tables and add foreign key constraint
* Improve the error handling to support custom handlers

## License

See the LICENSE file (GPL v2)

