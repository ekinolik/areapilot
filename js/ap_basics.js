/* DD Roundies Code */
DD_roundies.addRule("#header, #subhead, .likeit","0 0 8px 8px",true);
DD_roundies.addRule("#timeline","8px 8px 0 0",true);
DD_roundies.addRule("#inner","12px",true);
DD_roundies.addRule(".likebox, a.minievent span.numlikes","8px",true);

$(document).ready(function() {

      $("a.likeit").bind('click', function() {
	 vote($(this).attr('name'), 'l', 'e');
	 return false;
      });

      $("a.attendthis").bind('click', function() {
	 vote($(this).attr('name'), 'a', 'e');
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
         alert('wtf');
      }
   });
}

function readRating(json) {
   if (json.error.length > 0) {
      alert(json.error);
      return false;
   }

   updateRating(json.id, json.rating);

   return true;
}

function updateRating(e, v) {
   $("span#numlikes_"+e).text(v);
}
