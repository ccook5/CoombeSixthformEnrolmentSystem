<?php 
require_once 'config.inc.php';
require_once 'header.inc.php';
require_once 'select_widget.php';

print_header($title = 'Coombe Sixth Form Enrolment - GCSE Results', 
			$hide_title_bar = false,
			$script = "
	$(document).ready(function() {

". create_select_builder('build_student_type_select', 'SELECT* FROM StudentTypes', 'student_types', 'id', 'CourseType')."

/** This is run when a user clicks Edit or Save. 
  *
  * We take the value from some cells and create some select boxes with
  * those entries pre-selected.
  *
  * We trigger the above function to update the grade select box depending 
  * on the pre-selected value from GCSE_Type.
  *
  * Finally, we change the Edit button to a Save button. When clicked, the 
  * callback functions action depends on the value of this.
  */
		function editRow ( oTable, nRow, add ) {
			var aData = oTable.fnGetData(nRow);
			var jqTds = $('>td', nRow);
			jqTds[0].innerHTML = '<input type=\"text\" value=\"'+aData[0]+'\">';
			jqTds[1].innerHTML = '<input type=\"text\" value=\"'+aData[1]+'\">';
			jqTds[2].innerHTML = '<input type=\"text\" value=\"'+aData[2]+'\">';
			jqTds[3].innerHTML = '<input type=\"text\" value=\"'+aData[3]+'\">';
			jqTds[4].innerHTML = '<input type=\"text\" value=\"".$config['current_year']."\" disabled=\"disabled\" >';

// Col 6 doesnt need updating
			if (add == true) {
				jqTds[6].innerHTML = '<button class=\"edit\">Add Student</button>';
			}
			else
			{
				jqTds[6].innerHTML = '<button class=\"edit\">Save Student Details</button>';
			}
		}
				
/** Post the data from this row to ajax_update_students_results.php via AJAX.
  */
		function saveRow ( oTable, nRow, action ) {
			var jqInputs  = $('input',  nRow);
			var jqSelects = $('select', nRow);

			var act       = '';
			if (action == 'Save') {
				act = 'update';
			} else {
				act = 'new';
			}
			var request = $.ajax({
				url: 'ajax_update_students_results.php',
				type: 'POST',
				data: { 
					action             : act,
					StudentID          : jqInputs[0].value,
					FirstName          : jqInputs[1].value,
					LastName           : jqInputs[2].value,
					PreviousInstitution: jqInputs[3].value,
					EnrolmentYear      : jqInputs[4].value
				},
				dataType: 'html'
			} );

			request.done(function(msg) {
				$('#debug').html( msg);
			} );

			request.fail(function(jqXHR, textStatus) {
			  alert( 'Request failed: ' + textStatus );
			} );
			
			oTable.fnUpdate( jqInputs[0].value, nRow, 0, false );
			oTable.fnUpdate( jqInputs[1].value, nRow, 1, false );
			oTable.fnUpdate( jqInputs[2].value, nRow, 2, false );
			oTable.fnUpdate( jqInputs[3].value, nRow, 3, false );
			oTable.fnUpdate( jqInputs[3].value, nRow, 4, false );
			//oTable.fnUpdate( jqSelects[0].options[jqSelects[0].selectedIndex].value, nRow, 4, false );
			
			oTable.fnUpdate( '<button class=\"edit\">Edit Student</button>', nRow, 5, false );
			oTable.fnDraw();
		}

		var nEditing = null;

		var studentTable = $('#students').dataTable( {
			'bProcessing': true,
			'sAjaxSource': 'get_students.php',
			'sScrollY'   : '160px',
			'bPaginate'  : false,
			'fnRowCallback': function( nRow, aData, iDisplayIndex ) {
				$('td:eq(5)', nRow).html( '<button class=\"edit_results\">Edit Results</button>' );
				$('td:eq(6)', nRow).html( '<button class=\"edit\">Edit Student</button>' );
				$('td:eq(7)', nRow).html( '<button class=\"delete\">Delete</button>' );
			},
			'aoColumnDefs': [
				{ 'bVisible': false, 'aTargets': [5] }
			//  { 'sClass'  : 'center', 'aTargets': [ -1, -2 ] }
			]
		} );
		
//		makes buttons into jquery buttons
		$(\".edit_results\").button();
		$(\".edit\").button();
		$(\".delete\").button();

		function load_results_iframes( StudentID )
		{			
			$('#students_results').attr('src','/students_results.php?student_id='+StudentID);
			$('#average_results').attr('src','/enrolment.students.average_results.php?StudentID='+StudentID);
		}
		
		/* Add a click handler to the rows - this could be used as a callback */
		$('#students tbody').click( function( event )
		{
			$(studentTable.fnSettings().aoData).each(function (){
				$(this.nTr).removeClass('row_selected');
			});
			$(event.target.parentNode).addClass('row_selected');
			
			load_results_iframes($(event.target.parentNode).find('td:first').html());
		} );
	
		$('#new_student').click( function (e) {
			e.preventDefault();
			
			var aiNew = studentTable.fnAddData( [ '', '', '', '', '',
				'<button class=\"results\">Edit Results</button>',
				'<button class=\"edit\">Add Student</button>', '<button class=\"delete\">Delete</button>' ] );
			var nRow = studentTable.fnGetNodes( aiNew[0] );
			editRow( studentTable, nRow, true );
			nEditing = nRow;
		} );
		
		$('#students .edit_results').live('click', function (event) {
			event.preventDefault();
			load_results_iframes($(event.target.parentNode).parent().find('td:first').html());
		} );
		
		$('#students .delete').live('click', function (e)
		{
			e.preventDefault();
			
			var nRow     = $(this).parents('tr')[0];
			var jqTds    = $('>td', nRow);
			
			var act      = 'delete';
			var ID       = jqTds[0].innerHTML;
			
			var request = $.ajax({
				url: 'ajax_update_students.php',
				type: 'POST',
				data: {action : act, StudentID : ID },
				dataType: 'html'
			});

			request.done(function(msg) {
				$('#debug').html( msg + 'test');
			});

			request.fail(function(jqXHR, textStatus) {
			  alert( 'Request failed: ' + textStatus );
			});
			
			request = $.ajax({
				url: 'remove_all_gcse_results_for_students.php',
				type: 'POST',
				data: {StudentID : ID },
				dataType: 'html'
			});

			request.done(function(msg) {
				$('#debug').html( msg + 'test');
			});

			request.fail(function(jqXHR, textStatus) {
			  alert( 'Request failed: ' + textStatus );
			});
			
			studentTable.fnDeleteRow( nRow );
		} );
		
		$('#students .edit').live('click', function (e) {
			e.preventDefault();
			
			/* Get the row as a parent of the link that was clicked on */
			var nRow = $(this).parents('tr')[0];
			
			if ( nEditing !== null && nEditing != nRow ) {
				/* Currently editing - but not this row - restore the old before continuing to edit mode */
				restoreRow( studentTable, nEditing );
				editRow( studentTable, nRow, false );
				nEditing = nRow;
			}
			else if ( nEditing == nRow && this.innerHTML == 'Save Student Details') {
				/* Editing this row and want to save it */
				saveRow( studentTable, nEditing, 'Save' );
				nEditing = null;
			}
			else if ( nEditing == nRow && this.innerHTML == 'Add Student') {
				/* Editing this row and want to save it */
				saveRow( studentTable, nEditing, 'Add' );
				nEditing = null;
			}
			else {
				/* No edit in progress - lets start one */
				editRow( studentTable, nRow, false );
				nEditing = nRow;
			}
		} );
	} );");
?>

   <div class='block' >
    <table class='with-borders-horizontal'>
     <tr >
      <td>
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
       </td>
      </tr>
     </table>
   </div>
   
   <div id="debug" class="debug"></div>

     <iframe frameborder=0 style="width: 78%; height: 410px;" id="students_results"></iframe>
     <iframe frameborder=0 style="width: 18%; height: 410px; float: right;" id="average_results"></iframe>
 </body>
</html>