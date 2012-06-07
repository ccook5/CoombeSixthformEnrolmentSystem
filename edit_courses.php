<?php 
include 'header.inc.php';

print_header();

?>

  <form>
   <div class='block' >
    <table class='with-borders'>
     <tr >
      <td>Subject Name</td>
      <td>Course Type</td>
      <td>Blocks</td>
      <td></td>
	 </tr>
     <tr >
      <td><input type="text"></td>
      <td><label><input type="radio">AS-Level</label><label><input type="radio">IB</label><label><input type="radio"></label></td>
      <td><label><input type="checkbox">A</label><label><input type="checkbox">B</label><label><input type="checkbox">C</label><label><input type="checkbox">D</label><label><input type="checkbox">E</label><label><input type="checkbox">T</label></td>
	  <td><a href="">Commit</a></td>
	 </tr>
	</table>
   </div> 
  </form>
<?php

include 'footer.inc.php';
?>