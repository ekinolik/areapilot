jQuery(function($) {

      $("#selectstate").bind('change', function() {
	 queryString = 'state_id='+$(this).val();

	 $.ajax({
	    type: "POST",
	    data: queryString,
	    url: '/admin/get_locations.php',
	    success: function(xml) {
	       unloadSubAreas();
   
	       $("#zip").children().remove();
	       $("#city").children().remove();
	       $("#areacode").children().remove();
	       $("#county").children().remove();
	       $("#area").children().remove();
	       $("#parentarea").children().remove();

	       addToSelect($("zip_codes", xml), "zip", "#zip");
	       addToSelect($("cities", xml), "city", "#city");
	       addToSelect($("area_codes", xml), "area_code", "#areacode");
	       addToSelect($("counties", xml), "county", "#county");
	       addToSelect($("areas", xml), "area", "#area");
	       addToSelect($("parent_areas", xml), "parea", "#area");
	       addToSelect($("parent_areas", xml), "parea", "#parentarea");
	       loadSubAreas();
	    }
	 });
      });

      $(".addit").bind('click', function() {
	 var prefix = $(":input[name='prefix']", $(this).parent()).val();

	 $("select :selected", $(this).parent()).each(function() {
	    var id = $(this).val();
	    var name = $(this).text();

	    $("#newareas").append('<option value="'+prefix+id+'">'+name+'</option>');
	    $(this).remove();
	 });

	 return false;
      });

      $("#removeit").bind('click', function() {
	 $("select :selected", $(this).parent()).each(function() {
	    var prefix = $(this).val().substr(0, 3);
	    var id = $(this).val().substr(3);
	    var name = $(this).text();

	    var option = '<option value="'+id+'">'+name+'</option>';

	    var thediv = $(":input[value='"+prefix+"']").parent();
	    $("select", thediv).append(option);
	    $(this).remove();
	 });

	 return false;
      });

      $("#addarea").bind('submit', function() {
	 if ($("#areaname").val().length < 1 && $("#area").val() == 'na' ) {
	    alert('Invalid area name');
	    return false;
	 }

	 $("#newareas option").attr('selected', 'selected');
	 return true;
      });

      $("#subareasform").bind('submit', function() {
	 if ($("#parentarea").val().ength < 1) {
	    alert('Invalid parent area');
	    return false;
	 }

	 $("#subareas option").attr('selected', 'selected');
	 return true;
      });

      $("#btn-addarea").bind('click', function() {
	 $("#availareas :selected").each(function() {
	    var id = $(this).val();
	    var name = $(this).text();

	    $("#subareas").append('<option value="'+id+'">'+name+'</option>');
	    $(this).remove();
	 });

	 return false;
      });

      $("#btn-removearea").bind('click', function() {
	 $("#subareas :selected").each(function() {
	    var id = $(this).val().substr($(this).val().indexOf('_'));
	    var name = $(this).text();

	    var option = '<option value="'+id+'">'+name+'</option>';

	    $("#availareas").append(option);
	    $(this).remove();
	 });

	 return false;
      });

      $("#switchmasterarea").bind('click', function() {
	    $("#addtoarea").toggleClass('hidden');
	    $("#createnewarea").toggleClass('hidden');

	    if ($("#addtoarea").hasClass('hidden')) {
	       $(this).text('Add to an existing area');
	       $("#area").val('na');
	    } else {
	       $(this).text('Create a new area');
	       $("#areaname").val('');
	    }
	    return false;
      });

      $(".btn-clear").bind('click', function () {
	    $("select option", $(this).parent()).attr('selected', false);
	    return false;
      });

      $("#parentarea").bind('change', function () {
	    loadSubAreas();
      });

});

function addToSelect(xml, tag, obj) {
   if (tag == 'area') {
      $("#availareas").children().remove();
      $("#areas").children().remove();
      $(obj).append('<optgroup label="Sub-Areas"></optgroup>');
      $(obj).append('<option value="na">--Select an Area--</option>');
      $("#areas").append('<option value="na">--Select an Area--</option>');
      $("#areas").append('<optgroup label="Sub-Areas"></optgroup>');
   }
   if (tag == 'parea') {
      $(obj).append('<optgroup label="Parent Areas"></optgroup>');
      $(obj).append('<option value="na">--Select an Area--</option>');
      $("#parentarea").children().remove();
      $("#areas").append('<optgroup label="Parent Areas"></optgroup>');
   }
   $(tag,xml).each(function() {
	 id = $("id", $(this)).text();
	 name = $("name", $(this)).text();
	 if (tag == 'area') { // && $("parent", $(this)).text().length > 0) {
	    var prefix = $("parent", $(this)).text() + '_';
	    var l_cnt = $("location_count", $(this)).text();
	    if (l_cnt == '') l_cnt = 0;
	    $("#availareas").append('<option value="'+prefix+id+'">'+name+'</option>');
	    $("#areas optgroup[label='Sub-Areas']").append('<option value="'+id+'">'+name+' ('+l_cnt+')</option>');
	 }
	 if (tag == 'parea') {
	    var s_cnt = $("subarea_count", $(this)).text();
	    $("#areas optgroup[label='Parent Areas']").append('<option value="'+id+'">'+name+'</option>');
	 }
	 $(obj).append('<option value="'+id+'">'+name+'</option>');
   });
}
function unloadSubAreas() {
   $("#subareas option").each(function() {
	 var myID = $(this).val();
	 var myName = $(this).text();
	 var option = '<option value="'+myID+'">'+myName+'</option>';

	 $(this).remove();
	 $("#availareas").append(option);
   });
}

function loadSubAreas() {
   unloadSubAreas();

   var theParent = $("#parentarea").val();
   $("#availareas option").each(function() {
	 var myID = $(this).val();
	 var myName = $(this).text();
	 var myParent = myID.substring(0, myID.indexOf('_'));
	 var option = '<option value="'+myID+'">'+myName+'</option>';

	 if (theParent != myParent) return true;

	 $(this).remove();
	 $("#subareas").append(option);
   });
}
