$('body').on('blur', 'input[type="url"]', function(e) {
    var $input = $(e.currentTarget);
    var inputVal = $input.val();
    if (inputVal.length && inputVal.indexOf('http') !== 0 && inputVal.indexOf('https') !== 0) {
        $input.val( 'http://' + inputVal );
    }
});

$(function() {
	$('#addCheckerFormCSS, #addCheckerFormURL').on('blur', function(e) {
		var url = $('#addCheckerFormURL').val();
		var css = $('#addCheckerFormCSS').val();

		if (!(url.length && css.length))
			return;

		$.getJSON(
			'/get-content',
			{ "url": url, "css": css },
			function(data, status, xhr) {
				var $previewContainer = $('.addCheckerFormPreview');
				if (data.text) {
					$previewContainer
						.removeClass('hidden')
						.find('.alert')
							.text(data.text);
				} else {
					$previewContainer
						.addClass('hidden')
						.find('.alert')
							.text("");
				}
				if (data.status == "success") {
					$previewContainer.find('.alert').removeClass('alert-danger').addClass('alert-info');
					$('button[type="submit"]').removeAttr('disabled', 'disabled');
				}
				else if (data.status == "error") {
					$('button[type="submit"]').attr('disabled', 'disabled');
					$previewContainer.find('.alert').removeClass('alert-info').addClass('alert-danger');
				}
			}
		);
	});
});