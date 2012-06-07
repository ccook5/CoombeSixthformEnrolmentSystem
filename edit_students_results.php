<?php 
include 'header.inc.php';

print_header($title = 'Coombe Sixth form registration form.', $script = "
			$(document).ready(function() {
				var studentTable = $('#students').dataTable( {
					'bProcessing': true,
					'sAjaxSource': 'get_students.php',
					'sScrollY': '200px',
					'bPaginate': false,
					'fnRowCallback': function( nRow, aData, iDisplayIndex ) {
						$('td:eq(6)', nRow).html( '<a href=\"edit_student.php?id='+aData[0]+'\">Edit</a>' );
					},
					//'aoColumnDefs': [ {
					///	'sClass': 'center',
					//	'aTargets': [ -1, -2 ]
					//} ]
				} );
				/* Add a click handler to the rows - this could be used as a callback */
				$('#students tbody tr').click( function( e ) {
					if ( $(this).hasClass('row_selected') ) {
						$(this).removeClass('row_selected');
					} else {
						studentTable.$('tr.row_selected').removeClass('row_selected');
						$(this).addClass('row_selected');
					}
				});
			} );
			
     
			/* Apply the jEditable handlers to the table */
			studentTable.$('td').editable( '../examples_support/editable_ajax.php', {
				'callback': function( sValue, y ) {
					var aPos = studentTable.fnGetPosition( this );
					studentTable.fnUpdate( sValue, aPos[0], aPos[1] );
				},
				'submitdata': function ( value, settings ) {
					return {
						'row_id': this.parentNode.getAttribute('id'),
						'column': studentTable.fnGetPosition( this )[2]
					};
				},
				'height': '14px',
				'width': '100%'
			} );
			function fnClickAddRow() {
				$('#students').dataTable().fnAddData( [
					'', '', '', '', '2012', '', '' ] );
				
				giCount++;
			}");
?>

   <div class='block' >
    <table class='with-borders-horizontal'>
	<tr>
	 <td>
	  <form>
	   <label>Student ID:<input type=text></input></label>
	   <label>First Name:<input type=text></input></label>
	   <label>Surname:<input type=text></input></label>
	   <label>Previous Institution:<input type=text></input></label>
	  </form>
	 </td>
	</tr>
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