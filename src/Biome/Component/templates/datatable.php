<?php

$id				= $this->getId();
$var			= $this->getVar();
$object_list	= $this->getValue();
$column_list	= $this->getChildren('column');

?><div class="dataTable_wrapper"><table id="<?php echo $id ?>" class="<?php echo $this->getClasses(); ?>"><?php

/**
 * Building header.
 */
echo '<thead><tr>';

foreach($column_list AS $column)
{
	echo '<th>', $column->getTitle(), '</th>';
}

echo '</tr></thead>';

/**
 * Building rows.
 */
echo '<tbody>';

// foreach($object_list AS $v)
// {
// 	$this->setContext($var, $v);
//
// 	echo '<tr>';
//
// 	/**
// 	 * Render columns.
// 	 */
//
// 	foreach($column_list AS $column)
// 	{
// 		echo '<td>', $column->render(), '</td>';
// 	}
//
// 	echo '</tr>';
//
// 	$this->unsetContext($var);
// }

echo '</tbody>';

?></table></div><?php

$this->view->javascript(function() use($id) {

?>
$(document).ready(function() {
	$('#<?php echo $id; ?>').DataTable({
			responsive: true,
			serverSide: true,
			ajax: '<?php echo URL::getUri(), '?partial=', $id; ?>'
	});
});
<?php

});
