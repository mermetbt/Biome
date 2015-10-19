<?php

$var = $this->getVar();
$object_list = $this->getValue();
$field_list = $this->getChildren();

/**
 * Building rows.
 */
$body = '<tbody>';

$header_title = array();

foreach($object_list AS $v)
{
	$this->setContext($var, $v);

	$body .= '<tr>';

	/**
	 * Render columns.
	 */

	foreach($field_list AS $index => $fieldComponent)
	{
		if(method_exists($fieldComponent, 'getLabel'))
		{
			$header_title[$index] = $fieldComponent->getLabel();
		}
		else
		{
			$header_title[$index] = '';
		}

		$body .= '<td>';
		$body .= $fieldComponent->render();
		$body .= '</td>';
	}

	//$body .= '<td><a class="btn btn-sm btn-default" href="' . URL::fromRoute('cylinder', 'edit', $v->getId()) . '">Edit</a></td>';
	//$body .= '<td><a class="btn btn-sm btn-danger" href="' . URL::fromRoute('cylinder', 'delete', $v->getId()) . '">Delete</a></td>';

	$body .= '</tr>';

	$this->unsetContext($var);
}

$body .= '</tbody>';

/**
 * Building header.
 */
$header = '<thead><tr>';

foreach($header_title AS $title)
{
	$header .= '<th>' . $title . '</th>';
}

//$header .= '<th></th><th></th>'; // Buttons
$header .= '</tr></thead>';

?><div class="dataTable_wrapper"><table id="<?php echo $this->getId(); ?>" class="<?php echo $this->getClasses(); ?>"><?php

echo $header;

echo $body;

?></table></div><?php
