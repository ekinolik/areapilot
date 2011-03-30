$(document).ready(function() {
      $("a#rangeselect").click(function() {
	    $("#rangestart").focus();
	    return false;
      });

      $("div#submitform form.fullform input#date").datepicker();
      $("#rangestart").datepicker({
	    'dateFormat': 'yymmdd',
	    beforeShow: function(input, inst) {
	    	$("div#ui-datepicker-div").toggle();
	    },
	    onClose: function(input, inst) {
	    	$("div#ui-datepicker-div").toggle();
	    },
	    onSelect: function(selectedDate) {
	    	year = selectedDate.substr(0, 4);
	    	month = selectedDate.substr(4, 2);
	    	day = selectedDate.substr(6, 2);

		$("#rangeend").datepicker("option", "minDate",  new Date(year, month - 1, day));
		$("#rangestart").datepicker("hide");
		$("#rangeend").datepicker("show");
	    }
      });

      $("#rangeend").datepicker({
	    'dateFormat': 'yymmdd',
	    onSelect: function(selectedDate) {
	    	fullpath = location.pathname;

		if (fullpath.substr(0, 1) == '/') {
		   start = 1;
		} else {
		   start = 0;
		}
		
		slashPos = fullpath.indexOf('/',start+1);
		cat = fullpath.substr(start, slashPos - 1);
		if (cat.length > 0) {
		   cat = "/"+cat;
		}

	    	uri = cat+'/date-'+$("#rangestart").val()+'-'+$("#rangeend").val();
		location.href = uri;
	    }
      });

});
