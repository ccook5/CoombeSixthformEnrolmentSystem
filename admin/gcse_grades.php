<?php 
require_once('../config.inc.php');
require_once('../header.inc.php');
//require_once('../select_widget.php');

print_header($title = 'Coombe Sixth Form Enrolment - Admin', 
			$hide_title_bar = false, 
			$script = "", 
			$exclude_datatables_js = false, 
			$meta = "",
			$extra_script="gcse_grades.js.php");
?>
   <div class='block' >
       <span><a id="new" href="">Add Grade</a></span>
       <div id="dynamic">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="grades">
         <thead>
          <tr>
			<th width="2%">id</th>
			<th width="20%">Grade</th>
			<th width="5%">Points</th>
			<th width="20%">QualificationID</th>
			<th width="2%"></th>
			<th width="2%"></th>
          </tr>
         </thead>
         <tbody>
          <tr>
           <td colspan="5" class="dataTables_empty">Loading data from server</td>
          </tr>
         </tbody>
        </table>
	   </div>
   </div>
   
   <div id="debug" class="debug"></div>
 </body>
</html>