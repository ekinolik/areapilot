/* DD Roundies Code */
DD_roundies.addRule(".blockElement,.blockMsg","10px",true);
DD_roundies.addRule("#header, #subhead, .likeit","0 0 8px 8px",true);
DD_roundies.addRule("#categories a, button.btn-submit","8px",true);
DD_roundies.addRule("#loggedout a:hover, #loggedin a:hover","8px",true);
DD_roundies.addRule("#categories ul.submenu a","0 0 0 0",true);
DD_roundies.addRule("#timeline, #categories li.hasmenu a","8px 8px 0 0",true);
DD_roundies.addRule("#inner","12px",true);
DD_roundies.addRule(".likebox, a.minievent span.numlikes","8px",true);

$(document).ready(function() {

      // For main category dropdown menus
      $("#categories li.hasmenu a").bind("mouseover", function() {
         $("ul", $(this).parents("li.hasmenu")).show();
         $(this).parents("li.hasmenu").children("a").addClass("hovered");
      }).bind("mouseout",function() {
         $("ul", $(this).parents("li.hasmenu")).hide();
         $(this).parents("li.hasmenu").children("a").removeClass("hovered");
      });

      // For voting
      $("a.likeit").bind('click', function() {
	 vote($(this).attr('name'), 'l', 'e');
	 return false;
      });

      $("a.attendthis").bind('click', function() {
	 vote($(this).attr('name'), 'a', 'e');
	 return false;
      });


      $("a#futuredates").bind('click', function() {
	    $("li.selectdate.show").filter(':not(:animated)').animate({
	       height: 'toggle',
	    }, 400, function() {
	       $(this).addClass('hidden');
	       $(this).removeClass('show');
	       $(this).next().next().next().next().next().next().next().animate({
		  height: 'toggle',
	       }, 400, function() {
		  $(this).addClass('show');
		  $(this).removeClass('hidden');
	       });
	    });
      });
      
      $("a#prevdates").bind('click', function() {
	    $("li.selectdate.show").filter(':not(:animated)').animate({
	       height: 'toggle',
	    }, 400, function() {
	       $(this).addClass('hidden');
	       $(this).removeClass('show');
	       $(this).prev().prev().prev().prev().prev().prev().prev().animate({
		  height: 'toggle',
	       }, 400, function() {
		  $(this).addClass('show');
		  $(this).removeClass('hidden');
	       });
	    });
      });

      $("#btn-login a").click(function() {
	    blockThis("body",$("#modal-login"),nothing(),false,true);
	    return false;
      });

      $("#btn-signup").click(function() {
	    blockThis("body", $("#modal-signup"),nothing(), false, true);
	    return false;
      });

      $("a#change_my_password").click(function() {
	    if ($("input#change_password").val() != $("input#change_password2").val()) {
	    	alert('Passwords do not match');
		return false;
	    }
	    blockThis("body", $("#modal-signup"),nothing(), false, true);
	    blockThis("body", $("#modal-change-password"),nothing(), false, true);
	    return false;
      });
	
      $("#btn_submit_password").click(function() {
	    if ($("input#change_password").val() != $("input#change_password2").val()) {
	    	alert('Passwords do not match');
		return false;
	    }

	    if ($("input#change_password").val().length < 4) {
	    	alert('Password is too short');
		return false;
	    }
      });

      $("div#submitform form.fullform input.defaultvalue").bind('click', function() {
	    $(this).val('');
	    $(this).removeClass('defaultvalue');
      });

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

	    	$("div#ui-datepicker-div").toggle();
});

function vote(id, a, t) {
   var queryString = 'id='+id+'&t='+t+'&a='+a+'&r=j';

   $.ajax({
      type: "GET",
      url: '/vote.php?'+queryString,
      dataType: "json",
      cache: false,
      success: function(jsonString) {
         readRating(jsonString);
	 return true;
      },
      error: function(xmlhr, ts, et) {
         alert('strange things are afoot at the circle k');
      }
   });
}

function readRating(json) {
   if (json.error.error.length > 0) {
      if (json.error.errno == 4) {
	 blockThis("body",$("#modal-login"),nothing(),false,true);
      } else {
	 alert(json.error);
      }
      return false;
   }

   updateRating(json.id, json.rating);

   return true;
}

function updateRating(e, v) {
   $("span#numlikes_"+e).text(v);
}

// For generic blockUI functions, only for blocking specific elements
function blockThis(element, text, callback, roundit, overlayClose) {
	if(roundit == true) {
		var roundItCode = '10px';
	} else {
		var roundItCode = '0px';
	}
	$(element).block({
		message: text,
		centerY: false,
		css: {
			width: 'auto',
			position: 'fixed',
			'top': '5px'
		},
		overlayCSS: {
			background:'#333',
			'-webkit-border-radius': roundItCode,
			'-moz-border-radius': roundItCode
		},
		onBlock: function() {
			callback;
		}
	});
	if(overlayClose == true) {
		$('.blockOverlay').click(function() {
			$.unblockUI;
			$(element).unblock();
		});
	}
};

// generic unBlockUI function
function unBlockThis(element) {
	$(element).unblock();
};

// Dummy function for blockUI
function nothing() {
	return;
}

function nl2br(str) {
	return (str + '').replace(/(\r\n|\n\r|\r|\n)/g, '<br />');
}
