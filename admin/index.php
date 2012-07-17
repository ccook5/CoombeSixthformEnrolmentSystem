<?php 
require_once '../config.inc.php';
require_once '../header.inc.php';

print_header($title = 'Coombe Sixth Form Enrolment - Admin', 
			$hide_title_bar = false, 
			$script = "");
?>
  <div class='block' >
   <table class='with-borders-horizontal'>
    <tr >
     <td>
      <ul>
       <li><a href="config.php">Configuration</a>
	   </li>
	   <li>Blocks
	    <ul>
	     <li><a href="">Student Types</a></li>
	     <li><a href="">Block Names</a></li>
	     <li><a href="">Courses</a></li>
	     <li><a href="">Blocks</a></li>
	    </ul>
	   </li>
	   <li>Results
        <ul>
		 <li><a href="">GCSE Subjects</a></li>
		 <li><a href="">Qualifications</a></li>
		 <li><a href="">Grades</a></li>
		</ul>
	   </li>
	  </ul>
     </td>
    </tr>
   </table>
  </div>
   
  <div id="debug" class="debug"></div>
 </body>
</html>