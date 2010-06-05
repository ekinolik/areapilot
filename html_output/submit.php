<?php

print <<<EOF
   <div id="signup" class="full_form">
      <form method="post" action="submit.php" class="full">
	 <fieldset>
            <span class="errormsg">$error_class</span><br />
            <ol>
	       <li><label for="title">Title</label>
                  <input type="text" name="title" id="title" />
	       </li>
               <li><label for="tags">Tags</label>
                  <input type="text" name="tags" id="tags" />
               </li>
	       <li><label for="date">Date</label>
                  <input type="text" name="date" id="date" />
               </li>
	       <li><label for="time">Time</label>
		  <input type="text" name="time" id="time" />
                  <select name="meridian" id="meridian">
                     <option value="pm">PM</option>
                     <option value="am">AM</option>
                  </select>
               </li>
	       <li><label for="address">Address</label>
                  <input type="text" name="address" id="address" />
               </li>
	       <li><label for="zip">Zip</label>
                  <input type="text" name="zip" id="zip" />
               </li>
	       <li><label for="url">Link URL</label>
                  <input type="text" name="url" id="url" />
               </li>
	       <li><label for="description">Description</label>
                  <textarea name="description" id="description" rows="5" cols="20"></textarea>
	       </li>
               <li class="submit_line">
                  <button type="submit" class="submitter">Register</button>
               </li>
            </ol>
         </fieldset>
      </form>
   </div>
EOF;
?>
