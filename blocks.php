<?php 
require_once 'config.inc.php';
include 'header.inc.php';


print_header($title = 'Coombe Sixth Form Enrolment - Blocks', $hide_title_bar = false, $script = "
	$(document).ready(function() {
		var studentTable = $('#students').dataTable( {
			'bProcessing': true,
			'sAjaxSource': 'get_students.php',
			'sScrollY'   : '120px',
			'bPaginate'  : false,
			'fnRowCallback': function( nRow, aData, iDisplayIndex ) {
				$('td:eq(6)', nRow).html( '<a class=\"edit\" href=\"\">Edit</a>' );
				$('td:eq(7)', nRow).html( '<a class=\"delete\" href=\"\">Delete</a>' );
			}
			//'aoColumnDefs': [ {
			///	'sClass'  : 'center',
			//	'aTargets': [ -1, -2 ]
			//} ]
		} );
		
		
		/* Add a click handler to the rows - this could be used as a callback */
		$('#students tbody').click( function( event ) {
			$('#students_blocks').attr('src','/students_blocks.php');
			
			$(studentTable.fnSettings().aoData).each(function (){
				$(this.nTr).removeClass('row_selected');
			});
			$(event.target.parentNode).addClass('row_selected');
			
			$('#students_blocks').attr('src','/students_blocks.php?student_id='+$(event.target.parentNode).find('td:first').html());
			$('#average_results').attr('src','/enrolment.students.average_results.php?StudentID='+$(event.target.parentNode).find('td:first').html());
		}) ;
	});
");
?>
   <div class='block' >
    <table class='with-borders-horizontal'>
     <tr >
      <td>
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
			<th>Edit</th>
			<th>Delete</th>
          </tr>
         </thead>
         <tbody>
          <tr>
           <td colspan="9" class="dataTables_empty">Loading data from server</td>
          </tr>
         </tbody>
        </table>
	   </div>
      </td>
     </tr>
    </table>
   </div>


  <iframe frameborder=0 style="width: 79%; height: 480px;" id="students_blocks"></iframe>
  <iframe frameborder=0 style="width: 19%; height: 420px; float: right;" id="average_results"></iframe>
 </body>
</html>