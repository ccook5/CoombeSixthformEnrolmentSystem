<?php 

require_once 'config.inc.php';
include      'header.inc.php';

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = false, $script = "");

?>
  <table class='main-menu'>
   <tr>
    <td>
     <div class='menu-block'>
      <div class='menu-block-inner' >
	   <a href="blocks.php">Blocks</a> 
      </div>
     </div>
	</td>
	<td>
     <div class='menu-block'>
      <div class='menu-block-inner' >
	   <a href="results.php">GCSE Results</a>
      </div>
     </div>  
    </td>
   </tr>
  </table>
<?php
print_footer();
?>