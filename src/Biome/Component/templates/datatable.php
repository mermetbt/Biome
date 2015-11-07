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

$columns = array();
foreach($column_list AS $column)
{
	echo '<th>', $column->getTitle(), '</th>';
	$columns[] = array('name' => $column->getName(), 'searchable' => $column->isSearchable(), 'orderable' => $column->isOrderable());
}

echo '</tr></thead>';

/**
 * Building rows.
 */
echo '<tbody>';

echo '</tbody>';

?></table></div><?php

$datatable_options = array(
	'paging' => $this->hasPaging(),
	'searching' => $this->isSearchable(),
	'ordering' => $this->isOrderable(),
	'responsive' => TRUE,
	'serverSide' => TRUE,
	'ajax' => URL::getUri() . '?partial=' . $id,
	'columns' => $columns
);

$this->view->javascript(function() use($id, $datatable_options) {

?>
$(document).ready(function() {
	$('#<?php echo $id; ?>').DataTable(<?php echo json_encode($datatable_options); ?>);
});
<?php

});
