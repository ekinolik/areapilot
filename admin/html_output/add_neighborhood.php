<p class="msg"><?= $error_class->error ?></p>
	<form method="post" action="add_neighborhood.php" id="neighborhoods">
		<div id="neighborhoodadmin">
			<h2>Select State/Area</h2>
			<fieldset id="selectors">
				<ol class="clearfix">
					<li><label for="selectstate">Select a State : </label>
					   <select name="selectstate" id="selectstate">
                                              <option value="na">--Select a State--</option>
	<?php for ($i = 0, $iz = count($loc->states); $i < $iz; ++$i) : ?>
					      <option value="<?= $loc->states[$i]['id']; ?>"><?= strtoupper($loc->states[$i]['abbr']) ?></option>
	<? endfor; ?>
					   </select>
					</li>
					<li><label for="parentarea">Select Parent Area : </label>
					   <select name="parentarea" id="parentarea">
					   </select>
					</li>
					<li><label for="selectsubarea">Select Sub-Area : </label>
					   <select name="selectsubarea" id="selectsubarea">
					   </select>
					</li>
					<!-- <li><label for="selectcity">Select City : </label>
					   <select name="selectcity" id="selectcity">
					   </select>
					</li> -->
				</ol>
			</fieldset>
			<h2>Add Neighborhood</h2>
			<fieldset id="newneighborhood" class="clearfix">
				<div id="newhoodname">
					<label for="hoodname">New Neighborhood Name : </label><br />
					<input type="text" name="hoodname" id="hoodname" />
				</div>
				<div class="list">
					<h4>Applies To :</h4>
					<select multiple="multiple" name="applylist[]" id="applylist"></select>
				</div>
				<div class="clearfix">&nbsp;</div>
				<button type="submit" class="fancybutton" id="btn-addneighborhood" name="btn-addneighborhood">Add Neighborhood</button>
			</fieldset>
			<h2>Delete Neighborhoods</h2>
			<ul id="neighborhoodlist">
			</ul>
		</div>
	</form>
