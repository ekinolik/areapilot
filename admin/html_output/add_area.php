	<form method="post" action="add_area.php" id="addarea">
		<fieldset id="stateselect">
			<ol>
				<li><label for="selectstate">Select a State : </label>
				   <select name="selectstate" id="selectstate">
<?php for ($i = 0, $iz = count($loc->states); $i < $iz; ++$i) : ?>
				      <option value="<?= $loc->states[$i]['id']; ?>"><?= strtoupper($loc->states[$i]['abbr']) ?></option>
<? endfor; ?>
				   </select>
				</li>
			</ol>
		</fieldset>
<div id="addareas" class="clearfix">
	<h2>Add / Edit Areas</h2>
		<fieldset id="applyareas">
			<div class="list">
		       <h4>Areas you'll be adding : </h4>
		       <select multiple="multiple" name="newareas[]" id="newareas"></select><br />
		       <button name="remove" id="removeit">Remove</button>
			</div>
		</fieldset>
		<fieldset id="areapool" class="clearfix">
			<div class="list">
				<h4>Zipcodes</h4>
				<select multiple="multiple" name="zip" id="zip"></select><br />
				<input type="hidden" name="prefix" value="zi_" />
				<button name="zipadd" id="zipadd" class="addit">Add</button>
				<button name="clear-zip" id="clear-zip" class="btn-clear">Select None</button>
			</div>
			<div class="list">
				<h4>Cities</h4>
				<select multiple="multiple" name="city" id="city"></select><br />
				<input type="hidden" name="prefix" value="ci_" />
				<button name="cityadd" id="cityadd" class="addit">Add</button>
				<button name="clear-city" id="clear-city" class="btn-clear">Select None</button>
			</div>
			<div class="list">
				<h4>Area Codes</h4>
				<select multiple="multiple" name="areacode" id="areacode"></select><br />
				<input type="hidden" name="prefix" value="ac_" />
				<button name="areacodeadd" id="areacodeadd" class="addit">Add</button>
				<button name="clear-ac" id="clear-ac" class="btn-clear">Select None</button>
			</div>
			<div class="list">
				<h4>Counties</h4>
				<select multiple="multiple" name="county" id="county"></select><br />
				<input type="hidden" name="prefix" value="co_" />
				<button name="countyadd" id="countyadd" class="addit">Add</button>
				<button name="clear-co" id="clear-co" class="btn-clear">Select None</button>
			</div>
		</fieldset>
		<fieldset id="areaselect" class="clearfix">
			<ol>
				<li id="addtoarea" class="hidden"><label for="area">Add to area : </label>
					<select name="area" id="area">
						<option name="none" value="none">-- NONE --</option>
					</select>
			       <input type="hidden" name="prefix" value="ar_" />
				</li>
				<li id="createnewarea"><label for="areaname">Create New Area : </label><input type="text" name="areaname" id="areaname" /></li>
				<li>or<br />
					<a href="#" id="switchmasterarea">Add to existing area</a>
                                </li>
				<li><button type="submit" class="fancybutton" id="savechanges" name="savechanges">Save Area Changes</button></li>
			</ol>
		</fieldset>
	</form>
</div><!--// end #addareas //-->
<div id="editareas" class="clearfix">
	<h2>Sub-Area Admin</h2>
	<form name="subareas" id="subareasform" method="post" action="add_area.php">
		<fieldset>
			<ol>
				<li id="parentdropdown"><label for="parentarea">Load Parent Area : </label>
				   <select name="parentarea" id="parentarea"></select>
			       <input type="hidden" name="prefix" value="parentarea_" />
				</li>
				<li><label>Available Areas : </label>
					<select multiple="multiple" name="availareas" id="availareas"></select>
				</li>
				<li id="actionbuttons">
					<button type="button" id="btn-addarea" name="btn-addarea">&raquo;</button>
					<button type="button" id="btn-removearea" name="btn-removearea">&laquo;</button>
				</li>
				<li><label>Current Sub-Areas : </label>
					<select multiple="multiple" name="subareas[]" id="subareas"></select>
				</li>
				<li><div class="clearfix">&nbsp;</div></li>
				<li id="editsubmit"><button type="submit" class="fancybutton" name="btn-editsubmit" id="btn-editsubmit">Save Sub-Area Changes</button></li>
			</ol>
		</fieldset>
	</form>
</div><!--// end #editareas //-->

<div id="deleteareas" class="clearfix">
	<h2>Remove Area Admin</h2>
	<form name="deleteareas" id="deleteareasform" method="post" action="add_area.php">
		<fieldset>
			<ol>
				<li id="areadropdown"><label for="areas">Select an Area : </label>
				   <select name="areas" id="areas"></select>
				</li>
				<li><div class="clearfix">&nbsp;</div></li>
				<li id="deletesubmit"><button type="submit" class="fancybutton" name="btn-deletesubmit" id="btn-deletesubmit">Remove this area</button></li>
			</ol>
		</fieldset>
	</form>
</div><!--// end #deleteareas //-->
