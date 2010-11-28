jQuery(function($) {

      $("a.reply").live('click', function() {
	 $("div#reply_div").empty().remove();

	 var event_id = $("input#event_id").val();
	 var divid = $(this).parents('div').attr('id');
	 var parent_id = divid.substring(divid.lastIndexOf('_')+1);

	 var replyDiv = $(document.createElement('div')).attr('id', 'reply_div');
	 var replyForm = $(document.createElement('form')).attr('method', 'post').attr('action', 'comment.php').addClass('comment').addClass('reply');
	 var replyFieldset = $(document.createElement('fieldset'));
	 var replyOL = $(document.createElement('ol'));
	 var replyLI = $(document.createElement('li'));
	 replyLI.append($(document.createElement('label')).attr('for', 'add_reply').text('reply'));
	 replyLI.append($(document.createElement('input')).attr('name', 'event_id').attr('id', 'reply_id').attr('type', 'hidden').val(event_id));
	 replyLI.append($(document.createElement('input')).attr('name', 'parent_id').attr('id', 'parent_id').attr('type', 'hidden').val(parent_id));
	 replyLI.append($(document.createElement('textarea')).attr('name', 'add_comment').attr('id', 'add_reply'));


	 replyOL.append(replyLI);
	 replyFieldset.append(replyOL);
	 replyForm.append(replyFieldset);
	 replyDiv.append(replyForm);
	 
	 var replyLI = $(document.createElement('li')).addClass('submit_line');
	 var replyButton = $(document.createElement('button')).attr('type', 'submit').addClass('submitter').text('Submit Comment');

	 replyLI.append(replyButton);
	 replyOL.append(replyLI);

	 $(this).parents('div').append(replyDiv);

	 return false;
      });

      $("form.comment.reply").live('submit', function() {
	    var comment = $("#add_reply", $(this)).val();
	    var event_id = $("#reply_id", $(this)).val();
	    var parent_id = $("#parent_id", $(this)).val();
	    var queryString = "event_id="+encodeURIComponent(event_id)+"&add_comment="+encodeURIComponent(comment)+"&parent_id="+encodeURIComponent(parent_id);

	    submitComment(queryString);

	    $("#reply_div").empty().remove();
	    return false;
      });

      $("form.comment.parent").bind('submit', function() {
	    var comment = $("textarea#add_comment").val();
	    var event_id = $("form.comment.parent input#event_id").val();
	    var queryString = "event_id="+encodeURIComponent(event_id)+"&add_comment="+encodeURIComponent(comment);
	    
	    submitComment(queryString);

	    $("textarea#add_comment").val('');
	    return false;
      });

      $("a.expand").live('click', function() {
	    $(this).removeClass('expand').addClass('loading_comments');
	    $(this).text('Loading...');
	    var comment = $(this).parents('div').attr('id');
	    var comment_id = comment.substring(8);

	    $.ajax({
	       type: "POST",
	       data: "gch="+comment_id,
	       url: '/comment.php',
	       success: function(jsonString) {
		  for (var i = jsonString.comments.length - 1; i >= 0; i--) {
		  	displayReply(jsonString.comments[i]);
		  }

		  return true;
	       },
	       complete: function(xmlhr, ts) {
		  var tmp = $("div#"+comment);
		  $("a.loading_comments", tmp).html('&laquo; Hide');
		  $("a.loading_comments", tmp).removeClass('looading_comments').addClass('contract');
	       }
	    });

	    return false;
      });

      $("a.contract").live('click', function() {
	    $(this).removeClass('contract').addClass('expand');
	    var parentId = $(this).parents('div').attr('id');

	    var count = 0;
	    while($("div#"+parentId).next('div').hasClass('reply_comment')) {
	       $("div#"+parentId).next('div').empty().remove();
	       ++count;
	    }

	    if (count == 1)
	       $(this).html('1 reply &raquo;');
	    else
	       $(this).html(count+' replies &raquo;');

	    return false;
      });

      function displayReply(jsonComment) {
	    var newId = jsonComment.id;
	    var newUsername = jsonComment.username;
	    var newTime = jsonComment.time;
	    var message = nl2br(jsonComment.comment);
	    var parentId = jsonComment.parent;
	    var parentIdInt = parseInt(parentId);
	    var newAge = jsonComment.age;

	    var newCommentDiv = $(document.createElement('div')).attr('id', 'comment_'+newId);
	    var commentUsername = $(document.createElement('span')).addClass('username').text(newUsername);
	    var commentTime = $(document.createElement('span')).addClass('time').text(newAge);
	    var commentMessage = $(document.createElement('div')).addClass('message').html(message);
	    $(newCommentDiv).append(commentUsername);
	    $(newCommentDiv).append(commentTime);
	    $(newCommentDiv).append($(document.createElement('br')));
	    $(newCommentDiv).append(commentMessage);
	    $(newCommentDiv).addClass('comment');

	    if (parentIdInt > 0) {
		  $(newCommentDiv).addClass('reply_comment');
		  $(newCommentDiv).css('paddingLeft', '30px');
		  $("div#comment_"+parentId).after(newCommentDiv);
	    } else {
		  $("div#comment_container").children("div:last").after(newCommentDiv);
	    }

	    return true;
      }
	    
      function submitComment(queryString) {
	    $.ajax({
               type: "POST",
               data: queryString,
               url: '/comment.php',
               success: function(jsonString) {

		  displayReply(jsonString);
               }
	    });
      }

});
