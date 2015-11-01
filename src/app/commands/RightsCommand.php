<?php

use Biome\Core\Command\AbstractCommand;

class RightsCommand extends AbstractCommand
{
	/**
	 * @description Set the an user as administrator.
	 * @param mail Mail of the user.
	 */
	public function setAdmin($mail)
	{
		$this->output->writeln(sprintf('Set user <info>%s</info> as Administrator', $mail));

		
	}
}
