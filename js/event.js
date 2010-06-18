jQuery(function($) {

      $("a.reply").live('click', function() {
	 $("div#reply_div").remove();

	 var event_id = $("input#event_id").val();
	 var divid = $(this).parent().attr('id');
	 var parent_id = divid.substring(divid.lastIndexOf('_')+1);

	 var replyDiv = $(document.createElement('div')).attr('id', 'reply_div');
	 var replyForm = $(document.createElement('form')).attr('method', 'post').attr('action', 'comment.php').attr('class', 'comment');
	 var replyFieldset = $(document.createElement('fieldset'));
	 var replyOL = $(document.createElement('ol'));
	 var replyLI = $(document.createElement('li'));
	 replyLI.append($(document.createElement('label')).attr('for', 'add_reply').text('Reply'));
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

	 $(this).parent().append(replyDiv);

	 return false;
      });
});
