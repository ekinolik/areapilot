<?php

print <<<EOF
   <div id="login" class="full_form">
      <form method="post" action="login.php" class="full">
	 <fieldset>
            <span class="errormsg">$error_class</span><br />
            <ol>
	       <li><label for="username">Username</label>
                  <input type="text" name="username" id="username" />
	       </li>
               <li><label for="password">Password</label>
		  <input type="password" name="password" id="password" />
               </li>
               <li class="submit_line">
                  <button type="submit" class="submitter">Log in</button>
               </li>
            </ol>
         </fieldset>
      </form>
   </div>
EOF;
?>
