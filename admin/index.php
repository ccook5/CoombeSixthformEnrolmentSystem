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
	     <li><a href="studenttypes.php">Student Types</a></li>
	     <li><a href="blocknames.php">Block Names</a></li>
	     <li><a href="courses.php">Courses</a></li>
	     <li><a href="blocks.php">Blocks</a></li>
	    </ul>
	   </li>
	   <li>Results
        <ul>
		 <li><a href="gcse_subjects.php">GCSE Subjects</a></li>
		 <li><a href="gcse_qualifications.php">Qualifications</a></li>
		 <li><a href="gcse_grades.php">Grades</a></li>
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