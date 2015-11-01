<?php

use Biome\Core\Command\AbstractCommand;

class RightsCommand extends AbstractCommand
{
	/**
	 * @description Create the Administrator role with all the access rights.
	 */
	public function createAdminRole()
	{
		try
		{
			$admin = Role::get(1);
			$this->output->writeln('The administrator role already exists!');
		} catch(\Exception $e)
		{
			$admin = new Role();
			$admin->role_id = 1;
		}

		$rights = \Biome\Biome::getService('rights');

		/**
		 * Routes allowing.
		 */
		$router = \Biome\Biome::getService('router');

		foreach($router->getRoutes() AS $route)
		{
			$method		= $route['method'];
			$controller	= $route['controller'];
			$action		= $route['action'];

			$rights->setRoute($method, $controller, $action);
		}

		/**
		 * Objects allowing.
		 */
		$objects = \Biome\Core\ORM\ObjectLoader::getObjects();
		foreach($objects AS $object_name => $object)
		{
			$rights->setObject($object_name);

			foreach($object->getFieldsName() AS $attribute_name)
			{
				$rights->setAttribute($object_name, $attribute_name);
			}
		}

		$admin->role_name = 'Administrator';
		$admin->role_rights = $rights->exportToJSON();

		if(!$admin->save())
		{
			$this->output->writeln('Unable to create the administrator role!');
			$this->output->writeln(print_r($admin->getErrors(), TRUE));
			return FALSE;
		}

		$this->output->writeln('Administrator role created!');

		return TRUE;
	}

	/**
	 * @description Set the an user as administrator.
	 * @param mail Mail of the user.
	 */
	public function setAdmin($mail)
	{
		$this->output->writeln(sprintf('Set user <info>%s</info> as Administrator', $mail));

		$admin_role = Role::get(1);
		if(empty($admin_role))
		{
			$this->output->writeln('No Administrator role is in the database!');
			return FALSE;
		}

		$user = new User();
		$user->mail = $mail;
		$user->fetch('mail');

		if(empty($user->getId()))
		{
			$this->output->writeln(sprintf('User with the mail address <info>%s</info> not found in in the database!', $mail));
			return FALSE;
		}

		$ur = new UserRole();
		$ur->user = $user;
		$ur->role = $admin_role;

		if(!$ur->save())
		{
			$this->output->writeln('Unable to associate the administrator role!');
			$this->output->writeln(print_r($ur->getErrors(), TRUE));
			return FALSE;
		}

		$this->output->writeln('User associated!');

		return TRUE;
	}
}
