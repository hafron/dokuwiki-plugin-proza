jQuery(document).ready(function() {
	//formularz zdarzenia
	var ids = ['assumptions', 'summary'];

	for (var i = 0; i < ids.length; i++) {
		var textarea = jQuery("#proza_form #" + ids[i]);
		if (textarea.length > 0) {
			textarea.before('<div id="toolbar'+ids[i]+'"></div>');
			if (textarea.parents("form").find("input[name=id]").length === 0) {
				textarea.before('<input type="hidden" name="id" value="'+bds.gup('id')+'" />');
			}
			initToolbar('toolbar'+ids[i], ids[i], toolbar);
		}
	}

	jQuery("#proza_form .date").focusout(function() {
		var $this = jQuery(this);
		var date = $this.val();

		var $row = $this.parents(".proza_row");
		var $normalized_date = $row.find(".normalized_date");

		if (date === '') {
			$normalized_date.text("");
			return;
		}

		jQuery.post(
			DOKU_BASE + "lib/exe/ajax.php",
			{
				call: 'plugin_proza',
				name: 'local',
				date: date
			},
			function(data) {
				$row.find(".error").remove();
				if (data.status == 'success') {
					$normalized_date.text(data.date);
					if ($this.attr("id") == 'plan_date')
						jQuery("#proza_event").attr("class", data.class);
				} else {
					jQuery('<div class="error">'+data.msg+'</div>').prependTo($row.find(".proza_cell"));
					$normalized_date.text("");
				}
			},
			'json'
		);
	});

	$input_state = jQuery("#proza_form #state");
	if ($input_state.length > 0) {
		$summary = jQuery("#proza_form #summary").parents(".proza_row");

		if ($input_state.val() == '0')
			$summary.hide();
		$input_state.change(function() {
			if (jQuery(this).val() == '0')
				$summary.hide();
			else
				$summary.show();
		});
	}
});
