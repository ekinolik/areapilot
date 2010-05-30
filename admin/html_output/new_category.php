    <h2 class="pageheader">Create New Category</h2>
<?php

$opts = '            <option value="na">--No Parent Category--</option>'."\n";
for ($i = 0, $iz = count($categories); $i < $iz; ++$i) {
   $opts .= '            ';
   $opts .= '<option value="'.$categories[$i]['id'].'">'.$categories[$i]['name'].'</option>'."\n";
}
?>
    <p class="msg"><?= $msg ?></p><br />
    <form id="category_form" class="fullpageform" method="post" action="new_category.php">
		<fieldset>
			<ol>
				<li><label>Type</label>
				      <a id="n-services" class="servicetype active typeservices">Services</a>
				      <a id="n-goods" class="servicetype typegoods">Goods</a>
				      <a id="n-teaching" class="servicetype typeteaching">Hobbies</a>
                                      <input type="hidden" name="type" id="type_id" value="2" />
				</li>
				<li><label for="parent">Parent Category</label><select name="parent" id="name"><?= $opts; ?></select></li>
				<li><label for="name">Category Name</label><input type="text" name="name" id="name" /></li>
			</ol>
		</fieldset>
		<fieldset>
			<ol>
				<li class="submit_line"><button type="submit" class="submitter">Create Category</button></li>
			</ol>
		</fieldset>
    </form>
