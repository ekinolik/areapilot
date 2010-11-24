<?php

if ( ! defined('HTMLCLASS')) require(LIB_DIR.'HTML.php');

$header = HTML::body_header('Create a New Account');
$footer = HTML::body_footer();

print <<<EOF
$header
   <div id="signup" class="full_form">
      <form method="post" action="signup.php" class="full">
	 <fieldset>
            <span class="errormsg">$error_class</span><br />
            <ol>
	       <li><label for="username">Desired username</label>
                  <input type="text" name="username" id="username" />
               </li>
	       <li><label for="email">Email address</label>
                  <input type="text" name="email" id="email" />
               </li>
	       <li><label for="password">Password</label>
                  <input type="password" name="password" id="password" />
               </li>
	       <li><label for="password2">Confirm Password</label>
                  <input type="password" name="password2" id="password2" />
	       </li>
	       <li><label for="first_name">First Name</label>
                  <input type="text" name="first_name" id="first_name" />
               </li>
	       <li><label for="last_name">Last Name</label>
                  <input type="text" name="last_name" id="last_name" />
               </li>
               <li class="submit_line">
                  <button type="submit" class="submitter">Register</button>
               </li>
            </ol>
         </fieldset>
      </form>
   </div>
$footer
EOF;
?>
