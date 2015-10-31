<?php

$objects = \Biome\Core\ORM\ObjectLoader::getObjects();

$rights = \Biome\Core\Rights::loadFromJSON($this->getValue());

?>
<table class="table">
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
						<input type="checkbox" id="objects[<?php echo $object_name; ?>][object][view]" name="objects[<?php echo $object_name; ?>][object][view]" <?php echo ($rights->isObjectView($object_name))? 'checked="1"' : '' ?> value="1"/> View
					</label>
				</td>
				<td>
					<label class="checkbox-inline">
						<input type="checkbox" id="objects[<?php echo $object_name; ?>][object][create]" name="objects[<?php echo $object_name; ?>][object][create]" <?php echo ($rights->isObjectCreate($object_name))? 'checked="1"' : '' ?> value="1"/> Create
					</label>
				</td>
				<td>
					<label class="checkbox-inline">
						<input type="checkbox" id="objects[<?php echo $object_name; ?>][object][edit]" name="objects[<?php echo $object_name; ?>][object][edit]" <?php echo ($rights->isObjectEdit($object_name))? 'checked="1"' : '' ?> value="1"/> Edit
					</label>
				</td>
				<td>
					<label class="checkbox-inline">
						<input type="checkbox" id="objects[<?php echo $object_name; ?>][object][delete]" name="objects[<?php echo $object_name; ?>][object][delete]" <?php echo ($rights->isObjectDelete($object_name))? 'checked="1"' : '' ?> value="1"/> Delete
					</label>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<table class="table" id="table_<?php echo $object_name; ?>">
						<thead>
							<th>Attribute</th>
							<th><input type="checkbox" onClick="toggleCheckboxColumn(this, 'table_<?php echo $object_name; ?>', 'rights-view');"/></th>
							<th><input type="checkbox" onClick="toggleCheckboxColumn(this, 'table_<?php echo $object_name; ?>', 'rights-edit');"/></th>
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
										<input type="checkbox" class="rights-view" <?php echo ($rights->isAttributeView($object_name, $field_name))? 'checked="1"' : '' ?> id="objects[<?php echo $object_name; ?>][attributes][<?php echo $field_name; ?>][view]" name="objects[<?php echo $object_name; ?>][attributes][<?php echo $field_name; ?>][view]" value="1"/> View
									</label>
								</td>
								<td>
									<label class="checkbox-inline">
										<input type="checkbox" class="rights-edit" <?php echo ($rights->isAttributeEdit($object_name, $field_name))? 'checked="1"' : '' ?> <?php echo ($editable)? '':'disabled="1"' ?> id="objects[<?php echo $object_name; ?>][attributes][<?php echo $field_name; ?>][edit]" name="objects[<?php echo $object_name; ?>][attributes][<?php echo $field_name; ?>][edit]" value="1"/> Edit
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
