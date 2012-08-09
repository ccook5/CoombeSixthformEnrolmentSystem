<?php 
require_once '../config.inc.php';
require_once '../header.inc.php';
require_once '../select_widget.php';

print_header($title = 'Coombe Sixth Form Enrolment - Admin', 
			$hide_title_bar = false, 
			$script = "", 
			$exclude_datatables_js = false,
			$meta = "",
			$extra_script="studenttypes.js.php");
?>
   <div class='block' >
       <span><a id="new_studenttype" href="">Add Student Type</a></span>
       <div id="dynamic">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="studenttypes">
         <thead>
          <tr>
			<th width="10%">id</th>
			<th width="70%">Type</th>
			<th></th>
			<th></th>
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