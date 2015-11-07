<?php

$field = $this->getField();

$id = $this->getId();
$type = $this->getType();
$classes = $this->getClasses();
$name = $this->getName();
$value = $this->getValue();
$placeholder = $this->getPlaceholder();
$label = $this->getLabel();

?><div class="form-horizontal"><?php
?><div class="form-group"><?php

if(!empty($label))
{
	?><label for="<?php echo $id; ?>" class="control-label col-sm-2"><?php echo $label; ?></label> <?php
}

?><div id="<?php echo $id; ?>" class="col-sm-10 form-group"><?php

$viewable = $field === NULL || $this->rights->isAttributeView($field);

$editable = $field === NULL || ($field->isEditable() && $this->rights->isAttributeEdit($field));

if($viewable && $editable)
{
	?><p class="form-control-static"><a href="#" class="ajaxfield"><span class="content"><?php echo $value; ?></span> <i class="fa fa-pencil"></i></a></p><?php
}
else
if($viewable)
{
	?><p class="form-control-static"><?php echo $value; ?></p><?php
}
else
{
	?><p class="form-control-static"><i class="fa fa-ban"></i></p><?php
}

?></div><?php
?></div><?php
?></div><?php

if(!$viewable || !$editable)
{
	return;
}

$url = URL::getUri() . '?partial=' . $id;

$this->view->javascript(function() use($id, $url, $name) {
?>
$(document).ready(function() {
	var p = $('#<?php echo $id; ?>');

	$.fn.setCursorPosition = function(pos) {
		this.each(function(index, elem) {
			if (elem.setSelectionRange) {
			elem.setSelectionRange(pos, pos);
			} else if (elem.createTextRange) {
			var range = elem.createTextRange();
			range.collapse(true);
			range.moveEnd('character', pos);
			range.moveStart('character', pos);
			range.select();
			}
		});
		return this;
	};

	var submitFunc = function() {
		var text = p.find('.ajaxfield').val();
		var formgroup = p.parent();

		formgroup.removeClass('has-error');
		p.find('.help-block').html('');

		$.ajax({
			url: '<?php echo $url ?>',
			method: 'GET',
			dataType: 'json',
			data: {'<?php echo $name; ?>': text},
			success: function(data) {
				if(data.errors)
				{
					console.log(data.errors);
					var help = $('<span class="help-block"></span>');
					for(i in data.errors)
					{
						help.append(data.errors[i]);
					}
					formgroup.addClass('has-error');
					p.append(help);
					return;
				}

				var c = p.data('html');
				p.html(c);
				var content = p.find('.content');
				content.html(data.value);
				p.find('.ajaxfield').click(ev);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(textStatus);
			}
		});
	};

	var cancelFunc = function() {
		var text = p.find('.ajaxfield').val();
		var c = p.data('html');
		p.html(c);
		p.find('.ajaxfield').click(ev);
	}

	var ev = function() {
		p.data('html', p.html());
		var content = p.find('.content').html();

		var inputText = $('<input class="form-control ajaxfield" value="' + content + '">');

		var inputGroup = $('<span class="input-group-btn"/>');

		var submitButton = $('<button type="button" class="btn btn-default"><i class="glyphicon glyphicon-ok"></i></button>');
		var cancelButton = $('<button type="button" class="btn btn-default"><i class="glyphicon glyphicon-remove"></i></button>');

		var group = $('<div class="input-group"/>');

		inputText.keypress(function(event) {
			if(event.which != 13)
			{
				return;
			}
			event.preventDefault();
			submitFunc();
		});

		submitButton.click(submitFunc);
		cancelButton.click(cancelFunc);

		inputGroup.append(submitButton);
		inputGroup.append(cancelButton);

		group.append(inputText);
		group.append(inputGroup);

		p.html(group);

		inputText.focus();
		inputText.setCursorPosition(inputText.val().length*2);

		inputText.focusout(function() {
			setTimeout(function() {
				if(submitButton.is(':focus'))
				{
					return;
				}
				cancelFunc();
			}, 0);
		});
	};

	p.find('.ajaxfield').click(ev);
});
<?php
});

