<?php 
include 'header.inc.php';

print_header($title = 'Coombe Sixth form registration form.', $script = "
			$(document).ready(function() {
				/* Add a click handler to the rows - this could be used as a callback */
				$('#students tbody tr').click( function( e ) {
					if ( $(this).hasClass('row_selected') ) {
						$(this).removeClass('row_selected');
					} else {
						studentTable.$('tr.row_selected').removeClass('row_selected');
						$(this).addClass('row_selected');
					}
				});
				var studentTable = $('#students').dataTable( {
					'bProcessing': true,
					'sAjaxSource': 'get_students.php',
					'fnRowCallback': function( nRow, aData, iDisplayIndex ) {
						$('td:eq(6)', nRow).html( '<a href=\"edit_student.php?id='+aData[0]+'\">Edit</a>' );
					},
					//'aoColumnDefs': [ {
					///	'sClass': 'center',
					//	'aTargets': [ -1, -2 ]
					//} ]
				} );
			} );
			
     
			function fnClickAddRow() {
				$('#students').dataTable().fnAddData( [
					'', '', '', '', '2012', '', '' ] );
				
				giCount++;
			}");
?>

  <form>
   <div class='block' >
    <table class='with-borders-horizontal'>
     <tr >
      <td>
       <p><a href="javascript:void(0);" onclick="fnClickAddRow();">Add New Student</a></p>
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
			<th width="7%"></th>
          </tr>
         </thead>
         <tbody>
          <tr>
           <td colspan="6" class="dataTables_empty">Loading data from server</td>
          </tr>
         </tbody>
        </table>
       </td>
      </tr>
     </table>
   </div>
 </body>
</html>