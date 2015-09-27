<?php

class DataSet implements Iterator
{
	protected $_data = array();

	public function __construct() { }

	public function __set($varname, $value)
	{
		echo 'SET !', PHP_EOL;
	}

	/**
	 *  Méthodes de l'interface itérateurs.
	 */

	/* Retourne l'objet courant. */
	public function current()
	{
		return current($this->_data);
	}

	/* Retourne la clé de l'objet courant. */
	public function key()
	{
		return key($this->_data);
	}

	/* Avance d'un objet et retourne l'objet courant. */
	public function next()
	{
		return next($this->_data);
	}

	/* Repart du début. */
	public function rewind()
	{
		return rewind($this->_data);
	}

	/* Retourne TRUE s'il existe un objet courant. */
	public function valid()
	{
		return valid($this->_data);
	}
}
