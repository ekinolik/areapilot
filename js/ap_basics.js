attendees = null;
attendeesCols = 15;
nextAttendant = 0;

$(document).ready(function() {
      $("input.nospam").addClass('nobots');

      /* Rounded Corners */
      $(".blockElement, .blockMsg").corner("10px");
      $("#header, #subhead, .likeit").corner("br bl 8px");
      $("button.btn-submit").corner("8px");
      $("#loggedout a, #loggedin a").corner("8px");
      $("#timeline").corner("tl tr 8px");
      $("#inner").corner("12px");
      $("#page_footer").corner("tl tr 12px");
      $(".likebox, a.minievent span.numlikes, div.entry div.likebox span.numlikes").corner("8px");
      $("div#signup input, div#signup textarea").corner("5px");
      $("#categories > li > a").corner("tl tr 8px");
      $("#categories ul.submenu").corner("tr br bl 8px");
      $("#categories ul.submenu li:first-child a").corner("tr 8px");
      $("#categories ul.submenu li:last-child a").corner("br bl 8px");

      // For main category dropdown menus
      $("#categories > li.hasmenu > a, #categories li.hasmenu ul.submenu").bind("mouseover", function() {
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

      $("a.tweetit").bind('click', function() {
	 var tag_id = $(this).attr('id');
	 var id = tag_id.substr(tag_id.indexOf('_')+1);
	 vote(id, 'l', 'e');
	 return true;
      });

      $("a.attending").click(function() {
	    attendees = null;
	    nextAttendant = 0;

	    var pardiv = $(this).parents('div.entry').attr('id');
	    attendance($("#"+pardiv+" div.likebox a.likeit").attr('name'));
	    blockThis("body",$("#modal-attendees"),nothing(),false,true);
	    return false;
      });


      $("a#futuredates").bind('click', function() {
	    $("li.selectdate.show").filter(':not(:animated)').animate({
	       height: 'toggle'
	    }, 400, function() {
	       $(this).addClass('hidden');
	       $(this).removeClass('show');
	       $(this).next().next().next().next().next().next().next().animate({
		  height: 'toggle'
	       }, 400, function() {
		  $(this).addClass('show');
		  $(this).removeClass('hidden');
	       });
	    });
      });
      
      $("a#prevdates").bind('click', function() {
	    $("li.selectdate.show").filter(':not(:animated)').animate({
	       height: 'toggle'
	    }, 400, function() {
	       $(this).addClass('hidden');
	       $(this).removeClass('show');
	       $(this).prev().prev().prev().prev().prev().prev().prev().animate({
		  height: 'toggle'
	       }, 400, function() {
		  $(this).addClass('show');
		  $(this).removeClass('hidden');
	       });
	    });
      });

      $("#btn-login a").click(function() {
	    $("#login-username").val('');
	    $("#login-password").val('');
	    blockThis("body",$("#modal-login"),nothing(),false,true);
	    return false;
      });

      $("#btn-signup, span.formnote a#link-signup").click(function() {
	    blockThis("body", $("#modal-signup"),nothing(), false, true);
	    return false;
      });

      $("a#change_my_password").click(function() {
	    $("input#change_password").val('');
	    $("input#change_password2").val('');
	    blockThis("body", $("#modal-signup"),nothing(), false, true);
	    blockThis("body", $("#modal-change-password"),nothing(), false, true);
	    return false;
      });

      $("a#forgot_my_password").click(function() {
	    $("#forgot-username").val('');
	    $("#forgot-email").val('');
	    blockThis("body",$("#modal-forgot-password"),nothing(),false,true);
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

      $("div.attendees span.fml a").click(function() {
	    nextAttendant -= ((attendeesCols * 3) * 2);
	    createAttendanceCols(nextAttendant);
	    return false;
      });

      $("div.attendees span.fmr a").click(function() {
	    createAttendanceCols(nextAttendant);
	    return false;
      });

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

function attendance(id) {
   var queryString = 'id='+id;

   $.ajax({
      type: "GET",
      url: '/attendance.php?'+queryString,
      dataType: "json",
      cache: false,
      success: function(jsonString) {
	 displayAttendance(jsonString);
	 return true;
      },
      error: function(xmlhr, ts, et) {
	 alert('error! error!');
      }
   });
}

function displayAttendance(json) {
   if (json.error.length > 0) {
      alert(json.error);
      return false;
   }

   attendees = json.attendees;
   createAttendanceCols(nextAttendant);

   return true;
}

function createAttendanceCols(offset) {
   $("ul#attendees_1 li, ul#attendees_2 li, ul#attendees_3 li").empty().remove();
   
   var attendantEnd = nextAttendant + (attendeesCols * 3);
   if (nextAttendant <= 0) {
      $("div.attendees span.fml a").hide();
   } else {
      $("div.attendees span.fml a").show();
   }
   if (attendees.length <= attendantEnd) {
      $("div.attendees span.fmr a").hide();
   } else {
      $("div.attendees span.fmr a").show();
   }
   for (i = offset; i < attendantEnd; i++) {
      if (i >= attendees.length) break;
      if (attendees[i].username.length <= 0) continue;
      var myLI = $(document.createElement('li')).text(attendees[i].username);
      var idx = i % 3;
      $("ul#attendees_"+(idx+1)).append(myLI);
   }

   nextAttendant = attendantEnd;
}

function readRating(json) {
   if (json.error.length > 0) {
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
			'top': '25px'
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
		 $(".modal a.close_button").click(function() {
			$.unblockUI;
			$(element).unblock();
			return false;
	        });
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
