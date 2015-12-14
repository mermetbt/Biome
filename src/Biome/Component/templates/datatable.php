<?php

$id				= $this->getId();
$var			= $this->getVar();
$object_list	= $this->getValue();
$column_list	= $this->getChildren('column');

?><div class="dataTable_wrapper"><table id="<?php echo $id ?>" class="<?php echo $this->getClasses(); ?>" width="100%"><?php

/**
 * Building header.
 */
echo '<thead><tr>';

$columns = array();
$hasSearchRow = false;
foreach($column_list AS $column)
{
	echo '<th>', $column->getTitle(), '</th>';
	$columns[] = array('name' => $column->getName(), 'searchable' => $column->isSearchable(), 'orderable' => $column->isOrderable());

	if($column->isSearchable())
	{
		$hasSearchRow = true;
	}
}

echo '</tr>';
if($hasSearchRow)
{
	echo '<tr>';
	foreach($column_list AS $column)
	{
		if(!$column->isSearchable())
		{
			echo '<th></th>';
			continue;
		}
		echo '<th><input style="width:100px;" type="text" name="', $column->getName(),'" class="form-control input-xs"/></th>';
	}
	echo '</tr>';
}
echo '</thead>';

/**
 * Building rows.
 */
echo '<tbody>';

echo '</tbody>';

?></table></div><?php

$dom = '';

$dom .= 'l'; // Length changing input control

if($this->isSearchable())
{
	$dom .= 'f'; // Filtering input
}

$dom .= 'r'; // Processing display element
$dom .= 't'; // The table
$dom .= 'i'; // Table information summary
$dom .= 'p'; // Pagination control

$datatable_options = array(
	'dom' => $dom,
	'paging' => $this->hasPaging(),
	'searching' => $hasSearchRow,
	'ordering' => $this->isOrderable(),
	'responsive' => TRUE,
	'serverSide' => TRUE,
	'ajax' => URL::getUri() . '?partial=' . $id,
	'columns' => $columns
);

$this->view->javascript(function() use($id, $datatable_options) {

?>
$(document).ready(function() {
	var table = $('#<?php echo $id; ?>').DataTable(<?php echo json_encode($datatable_options); ?>);

	table.columns().every( function () {
        var that = this;

        $( 'input', this.header() ).on( 'keydown', function (event) {
			if(event.which != 13) return;
			event.preventDefault();
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
});
<?php

});
