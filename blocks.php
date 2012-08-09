<?php 
require_once 'config.inc.php';
require_once 'header.inc.php';
require_once 'footer.inc.php';
require_once 'select_widget.php';

print_header($title         = 'Coombe Sixth Form Enrolment - Blocks', 
			$hide_title_bar = false, 
			$script         = "

", $exclude_datatables_js = false, $meta = "", $extra_script = "/blocks.js.php");

?>
   <div class='block' >
       <p><a id="new_student" href="">Add New Student</a></p>
       <div id="dynamic">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="students">
         <thead>
          <tr>
			<th width="7%">Student ID</th>
			<th width="20%">First Name</th>
			<th width="20%">Surname</th>
			<th width="20%">Previous Institution</th>
			<th width="7%">Enrolment Year</th>
			<th width="20%">Student Type</th>
			<th></th>
			<th></th>
			<th></th>
          </tr>
         </thead>
         <tbody>
          <tr>
           <td colspan="9" class="dataTables_empty">Loading data from server</td>
          </tr>
         </tbody>
        </table>
	   </div>
   </div>
   
   <div id="debug" class="debug"></div>
   <div id="test">
    <iframe frameborder="0" style="width: 79%; height: 480px;" id="students_blocks"></iframe>
    <iframe frameborder="0" style="width: 19%; height: 420px; float: right;" id="average_results"></iframe>
   </div>

<?php
print_footer();
?>

