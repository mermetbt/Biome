<?php

namespace Biome\Core\ORM\Filter;

class FilterNode
{
	protected $operator;
	protected $operands;

	public function __construct($operator, array $operands)
	{
		$this->operator = $operator;
		$this->operands = $operands;
	}

	public function addOperand($operand)
	{
		$this->operands[] = $operand;
	}

	protected function getOperand($operand, $operandHandler)
	{
		if(empty($operand))
		{
			return NULL;
		}

		if($operand instanceof FilterNode)
		{
			return $operand->toSql($operandHandler);
		}

		return $operandHandler->handleOperand($operand);
	}

	public function toSql(OperandHandlerInterface $operandHandler)
	{
		$sql_list = array();
		foreach($this->operands AS $op)
		{
			$op = $this->getOperand($op, $operandHandler);
			if(!empty($op))
			{
				$sql_list[] = $op;
			}
		}

		if(empty($sql_list))
		{
			return '';
		}

		return '(' . join(' ' . $this->operator . ' ', $sql_list) . ')';
	}
}
