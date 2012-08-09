<?php 

require_once('config.inc.php');
require_once('header.inc.php');
require_once('footer.inc.php');

print_header($title          = 'Coombe Sixth Form Enrolment', 
             $hide_title_bar = false, 
		     $script         = "");

?>
  <table class='main-menu'>
   <tr>
	<td>
     <div class='menu-block'>
	   <a href="results.php">GCSE Results</a>
     </div>  
    </td>
    <td>
     <div class='menu-block'>
	   <a href="blocks.php">Blocks</a>
     </div>
	</td>
   </tr>
  </table>
<?php
print_footer();
?>