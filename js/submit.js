jQuery(function($) {
      $("div#submitform form.fullform input#date").datepicker();
      $("button.submitter").button();

      $("input#title").keyup(function() {
	 var uri_title = $("input#title").val().replace(/[^A-Za-z0-9 ]/g, '');
	 uri_title = uri_title.replace(/ +/g, ' ');
	 len = uri_title.length;
	 if ( ! $("input#title").hasClass('input_error')) {
	 	/* Nested in case I decide to add error messages later */
	 	if ( len > 60 ) {
	 		$("input#title").addClass('input_error');
		} else if ( len < 8 ) {
	 		$("input#title").addClass('input_error');
		}
	 } else if ( $("input#title").hasClass('input_error') && len <= 60 && len >= 8) {
	 	$("input#title").removeClass('input_error');
	 }
      });
});
