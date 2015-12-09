( ($) ->
	$.fn.ajaxField = (opts) ->
		opts = $.extend {}, $.fn.ajaxField.options, opts
		this.each ->
			$this = $(this)
			$.initAjaxField $this

	$.fn.ajaxField.options =
		id: ""
		url: ""
		name: ""

	$.initAjaxField = ($this) ->
		i = $this.find('.ajaxfield-input')
		i.click( () ->
			$.ajaxField_event_callback $this
		)

	$.ajaxField_event_callback = ($this) ->
		$this.data('html', $this.html());

		url = $this.data 'url'
		name = $this.data 'name'
		type = $this.data 'type'

		content = $this.find('.content').html();

		if type == 'textarea'
			regex = new RegExp('(<([^>]+)>)', 'ig');
			content = content.replace(regex, '');
			inputText = $('<textarea class="form-control ajaxfield-input">' + content + '</textarea>');
		else
		if type == 'many2one'
			inputText = $('<select class="form-control ajaxfield-input"></select>');
			identifier = $this.data 'id'
			if identifier
				inputText.append('<option value="'+identifier+'" selected="selected">'+content+'</option>');
		else
			inputText = $('<input type="'+type+'" class="form-control ajaxfield-input" value="' + content + '">');
			inputText.keypress((event) ->
				if event.which != 13
					return;
				event.preventDefault();
				$.submitFunc $this;
			);

		inputGroup = $('<span class="input-group-btn"/>');
		submitButton = $('<button type="button" class="btn btn-default"><i class="glyphicon glyphicon-ok"></i></button>');
		cancelButton = $('<button type="button" class="btn btn-default"><i class="glyphicon glyphicon-remove"></i></button>');
		group = $('<div class="input-group"/>');

		submitButton.click(() ->
					 $.submitFunc $this
		);
		cancelButton.click(() ->
					 $.cancelFunc $this
		);

		inputGroup.append(submitButton);
		inputGroup.append(cancelButton);

		group.append(inputText);
		group.append(inputGroup);

		$this.html(group);

		if type == 'textarea'
			autosize(inputText);
		else
		if type == 'many2one'

			formatM2OSelector = (content) ->
				if content.loading
					return content.name

				markup = "<div class='select2-result-field clearfix'>" +
						"<div class='select2-result-field_content'>"+content.name+"</div>" +
						"</div>";

				return markup

			formatM2OSelection = (content) ->
				return content.name || content.text;

			inputText.select2({
				placeholder: "",
				allowClear: true,
				ajax: {
					    url: $this.data 'url',
						dataType: 'json',
						delay: 250,
						data: (params) ->
							return {
								action: 'search',
								q: params.term,
								page: params.page
							};
						,
						processResults: (data, params) ->
							params.page = params.page || 1

							return {
									results: data.items,
									pagination: {
										more: (params.page * 30) < data.total_count
									}
							}
						,
						cache: true
				},
				escapeMarkup: (markup) -> return markup,
				minimumInputLength: 1,
				templateResult: formatM2OSelector,
				templateSelection: formatM2OSelection
			});
			return;

		inputText.focus();
		inputText.setCursorPosition(inputText.val().length*2);

		inputText.focusout(() ->
			setTimeout ( =>
				if submitButton.is(':focus')
					return;
				$.cancelFunc($this);
			), 0
		);

	$.submitFunc = ($this) ->
		text = $this.find('.ajaxfield-input').val();
		formgroup = $this.parent();

		formgroup.removeClass('has-error');
		$this.find('.help-block').html('');

		data = {
			action: 'save'
		}
		data[$this.data('name')] = text;

		$.ajax
			url: $this.data 'url'
			method: 'GET'
			dataType: 'json'
			data: data
			success: (data) ->
				if data.errors
					console.log(data.errors);
					help = $('<span class="help-block"></span>');
					for i in data.errors
						help.append(data.errors[i]);
					formgroup.addClass('has-error');
					$this.append(help);
					return;

				c = $this.data('html');
				$this.html(c);
				content = $this.find('.content');
				regex = new RegExp('\n', 'g');
				type = $this.data 'type'
				if data.content
					txt = data.content
					$this.data('id', data.value)
				else
					txt = data.value

				txt = txt.replace(regex, '\n<br>');
				content.html(txt);
				$this.find('.ajaxfield-input').click( () ->
						$.ajaxField_event_callback $this
					);
			error: (jqXHR, textStatus, errorThrown) ->
				console.log(textStatus);
				console.log(errorThrown);
				console.log(jqXHR);
				alert('An error occured! Try to reload the page and try again...');

	$.cancelFunc = ($this) ->
		text = $this.find('.ajaxfield-input').val();
		c = $this.data('html');
		$this.html(c);
		i = $this.find('.ajaxfield-input')
		i.click(() ->
		  $.ajaxField_event_callback $this
		);

	$.fn.setCursorPosition = (pos) ->
		this.each ->
			(index, elem) ->
				if elem.setSelectionRange
					elem.setSelectionRange(pos, pos);
				else
					if elem.createTextRange
						range = elem.createTextRange();
						range.collapse(true);
						range.moveEnd('character', pos);
						range.moveStart('character', pos);
						range.select();

) jQuery

$ ->
	$('.ajaxfield').ajaxField({});
	autosize($('textarea'));
