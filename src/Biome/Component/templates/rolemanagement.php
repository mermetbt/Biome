<?php

$objects = \Biome\Core\ORM\ObjectLoader::getObjects();

$router = \Biome\Biome::getService('router');
$routes = $router->getRoutes();

$rights = \Biome\Core\Rights::loadFromJSON($this->getValue());

?>
<div id="<?php echo $this->getId(); ?>">
<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="active"><a href="#role_routes" aria-controls="role_routes" role="tab" data-toggle="tab">Routes</a></li>
	<li role="presentation"><a href="#role_objects" aria-controls="role_objects" role="tab" data-toggle="tab">Objects</a></li>
</ul>
<div class="tab-content">

<div role="tabpanel" class="tab-pane fade in active" id="role_routes">
<table id="table_routes" class="table table-striped table-hover table-condensed">
	<thead>
		<tr>
			<th>Method</th>
			<th>Route</th>
			<th>Description</th>
			<th><input type="checkbox" onClick="toggleCheckboxColumn(this, 'table_routes', 'rights-routes');"/> <span> Access</span></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($routes AS $r)
		{
			$method = $r['method'];
			switch($method)
			{
				case 'GET':
					$label = 'label-info';
					break;
				case 'POST':
					$label = 'label-warning';
					break;
				case 'PUT':
					$label = 'label-success';
					break;
				case 'DELETE':
					$label = 'label-danger';
					break;
				default:
					$label = 'label-default';
			}
			?>
			<tr>
				<td><span class="label <?php echo $label; ?>"><?php echo $method; ?></span></td>
				<td><span>/<?php echo $r['controller']; ?>/<?php echo $r['action']; ?></td>
				<td></td>
				<td><input type="checkbox" class="rights-routes" <?php echo ($rights->isRouteAllowed($method, $r['controller'], $r['action'])) ? 'checked="1"' : '' ?> id="rights[routes][<?php echo $method; ?>][<?php echo $r['controller']; ?>][<?php echo $r['action']; ?>]" name="rights[routes][<?php echo $method; ?>][<?php echo $r['controller']; ?>][<?php echo $r['action']; ?>]" value="1"/></td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
</div>

<div role="tabpanel" class="tab-pane fade" id="role_objects">
<table class="table table-condensed">
	<thead>
		<tr>
			<th>Object</th>
			<th colspan="4">Authorization</th>
		</tr>
	</thead>

	<tbody>
	<?php

		foreach($objects AS $object_name => $object)
		{
			$attributes = $object->getFieldsName();

			?>
			<tr>
				<td rowspan="2"><strong><?php echo $object_name; ?></strong></td>
				<td>
					<label class="checkbox-inline">
						<input type="checkbox" id="rights[objects][<?php echo $object_name; ?>][object][view]" name="rights[objects][<?php echo $object_name; ?>][object][view]" <?php echo ($rights->isObjectView($object_name))? 'checked="1"' : '' ?> value="1"/> View
					</label>
				</td>
				<td>
					<label class="checkbox-inline">
						<input type="checkbox" id="rights[objects][<?php echo $object_name; ?>][object][create]" name="rights[objects][<?php echo $object_name; ?>][object][create]" <?php echo ($rights->isObjectCreate($object_name))? 'checked="1"' : '' ?> value="1"/> Create
					</label>
				</td>
				<td>
					<label class="checkbox-inline">
						<input type="checkbox" id="rights[objects][<?php echo $object_name; ?>][object][edit]" name="rights[objects][<?php echo $object_name; ?>][object][edit]" <?php echo ($rights->isObjectEdit($object_name))? 'checked="1"' : '' ?> value="1"/> Edit
					</label>
				</td>
				<td>
					<label class="checkbox-inline">
						<input type="checkbox" id="rights[objects][<?php echo $object_name; ?>][object][delete]" name="rights[objects][<?php echo $object_name; ?>][object][delete]" <?php echo ($rights->isObjectDelete($object_name))? 'checked="1"' : '' ?> value="1"/> Delete
					</label>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<table class="table table-striped table-condensed table-hover" id="table_<?php echo $object_name; ?>">
						<thead>
							<tr>
								<th>Attribute</th>
								<th><input type="checkbox" onClick="toggleCheckboxColumn(this, 'table_<?php echo $object_name; ?>', 'rights-view');"/></th>
								<th><input type="checkbox" onClick="toggleCheckboxColumn(this, 'table_<?php echo $object_name; ?>', 'rights-edit');"/></th>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach($attributes AS $field_name)
						{
							$field = $object->getField($field_name);

							$editable = $field->isEditable();

							?>
							<tr>
								<td><strong><?php echo $field->getLabel(); ?></strong> (<?php echo $field_name; ?>)</td>
								<td>
									<label class="checkbox-inline">
										<input type="checkbox" class="rights-view" <?php echo ($rights->isAttributeView($object_name, $field_name))? 'checked="1"' : '' ?> id="rights[objects][<?php echo $object_name; ?>][attributes][<?php echo $field_name; ?>][view]" name="rights[objects][<?php echo $object_name; ?>][attributes][<?php echo $field_name; ?>][view]" value="1"/> View
									</label>
								</td>
								<td>
									<label class="checkbox-inline">
										<input type="checkbox" class="rights-edit" <?php echo ($rights->isAttributeEdit($object_name, $field_name))? 'checked="1"' : '' ?> <?php echo ($editable)? '':'disabled="1"' ?> id="rights[objects][<?php echo $object_name; ?>][attributes][<?php echo $field_name; ?>][edit]" name="rights[objects][<?php echo $object_name; ?>][attributes][<?php echo $field_name; ?>][edit]" value="1"/> Edit
									</label>
								</td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				</td>
			</tr>
			<?php
		}
	?>
	</tbody>

</table>
</div>

</div>
</div>
<script>
function toggleCheckboxColumn(source, table_id, classname)
{
	var table = document.getElementById(table_id);

	input = table.getElementsByClassName(classname);
	for(var i=0, n=input.length; i<n; i++)
	{
		if(!input[i].disabled)
		{
			input[i].checked = source.checked;
		}
	}
}
</script>
