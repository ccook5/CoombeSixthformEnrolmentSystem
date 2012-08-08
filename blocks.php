<?php 
require_once 'config.inc.php';
require_once 'header.inc.php';
require_once 'footer.inc.php';
require_once 'select_widget.php';

print_header($title         = 'Coombe Sixth Form Enrolment - Blocks', 
			$hide_title_bar = false, 
			$script         = "
	var orig_student_type = null;
	var orig_student_name = null;
		
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
			jqTds[5].innerHTML = build_student_type_select(aData[5]);
// Col 6 doesnt need updating
			jqTds[7].innerHTML = '<button class=\"edit\">Save Student Details</button>';
			if (add == true) {
				jqTds[7].innerHTML = '<button class=\"edit\">Add Student</button>';
			}
			else
			{
				jqTds[7].innerHTML = '<button class=\"edit\">Save Student Details</button>';
			}
		}
		
/** Post the data from this row to ajax_update_students_results.php via AJAX.
  */
		function saveRow ( oTable, nRow, action ) {
			var jqInputs  = $('input', nRow);
			var jqSelects = $('select', nRow);
			
			var act = '';
			
			if (action == 'Save') {
				act = 'update';
			} else {
				act = 'new';
			}

			var request = $.ajax({
				url: 'api/ajax_update_students.php',
				type: 'POST',
				data: {action              : act,
				       StudentID           : jqInputs[0].value,
				       FirstName           : jqInputs[1].value,
				       LastName            : jqInputs[2].value,
				       StudentType         : jqSelects[0].options[jqSelects[0].selectedIndex].value,
				       PreviousInstitution : jqInputs[3].value,
				       EnrolmentYear       : jqInputs[4].value},
				dataType: 'html'
			});

			request.done(function(msg) {
			  $('#debug').html( msg );
			});

			request.fail(function(jqXHR, textStatus) {
			  alert( 'Request failed: ' + textStatus );
			});
			oTable.fnUpdate( jqInputs[0].value, nRow, 0, false );
			oTable.fnUpdate( jqInputs[1].value, nRow, 1, false );
			oTable.fnUpdate( jqInputs[2].value, nRow, 2, false );
			oTable.fnUpdate( jqInputs[3].value, nRow, 3, false );
			oTable.fnUpdate( jqInputs[4].value, nRow, 4, false );
			oTable.fnUpdate( jqSelects[0].options[jqSelects[0].selectedIndex].value, nRow, 5, false );
			
			oTable.fnUpdate( '<button class=\"edit\">Edit Student</button>', nRow, 7, false );
			oTable.fnDraw();
		}
		
		var nEditing = null;
		
		var studentTable = $('#students').dataTable( {
			'bProcessing': true,
			'sAjaxSource': 'get_students.php',
			'sScrollY'   : '120px',
			'bPaginate'  : false,
			'fnRowCallback': function( nRow, aData, iDisplayIndex ) {
				$('td:eq(6)', nRow).html( '<button class=\"edit_results\">Edit Blocks</button>' );
				$('td:eq(7)', nRow).html( '<button class=\"edit\">Edit Student</button>' );
				$('td:eq(8)', nRow).html( '<button class=\"delete\">Delete</button>' );
			}
		} );
		
//		makes html buttons into jquery buttons
		$(\".edit_results\").button();
		$(\".edit\").button();
		$(\".delete\").button();

		function load_iframes(StudentID)
		{
			$('#students_blocks').attr('src','/students_blocks.php?student_id='+StudentID);
			$('#average_results').attr('src','/enrolment.students.average_results.php?StudentID='+StudentID);
		}

/** Add a click handler to the rows. This adds a highlight to the currently selected row. 
  * 
  * This could also be used as a callback to do something with the row.
  */
		$('#students tbody').click( function( event ) {
			load_iframes( $(event.target.parentNode).find('td:first').html() );
			
			$(studentTable.fnSettings().aoData).each(function () {
				$(this.nTr).removeClass('row_selected');
			});
			$(event.target.parentNode).addClass('row_selected');
		} ) ;
	
		$('#students .edit_results').live('click', function (event) {
			event.preventDefault();
			
			load_iframes( $(event.target.parentNode).parent().find('td:first').html() );
		} );
	
		$('#new_student').click( function (e) {
			e.preventDefault();
			
			var aiNew = studentTable.fnAddData( [ '', '', '', '', '".$config["current_year"]."', '',
				'<button class=\"results\">Edit Results</button>',
				'<button class=\"edit\">Add Student</button>', '<button class=\"delete\">Delete</button>' ] );
			var nRow = studentTable.fnGetNodes( aiNew[0] );
			editRow( studentTable, nRow, true );
			nEditing = nRow;
		} );
/** Delete Click handler. Calls 'ajax_update_students_results.php 
  * via AJAX, then deletes the row in the datatable.
  */
		$('.delete').live('click', function (e)
		{
			e.preventDefault();
			
			var nRow     = $(this).parents('tr')[0];
			var jqTds    = $('>td', nRow);
			var act      = 'delete';
			var ID       = jqTds[0].innerHTML;
			
			var request = $.ajax({
				url: 'api/ajax_update_students.php',
				type: 'POST',
				data: {action : act, StudentID : ID },
				dataType: 'html'
			});

			request.done(function(msg) {
				$('#debug').html( msg);
			});

			request.fail(function(jqXHR, textStatus) {
			  alert( 'Request failed: ' + textStatus );
			});
			
			studentTable.fnDeleteRow( nRow );
		} );
		
		$('.edit').live('click', function (e)
		{
			/* Get the row as a parent of the link that was clicked on */
			var nRow = $(this).parents('tr')[0];
			
			if ( nEditing !== null && nEditing != nRow ) {
				/* Currently editing - but not this row - restore 
				 * the old before continuing to edit mode          */
				restoreRow( studentTable, nEditing );
				editRow( studentTable, nRow, false );
//				$('#students_blocks').fadeOut(3000);
				nEditing = nRow;
			}
			else if ( nEditing == nRow && this.innerHTML == 'Save Student Details') {
				/* Editing this row and want to save it */
				saveRow( studentTable, nEditing, 'Save' );
//				$('#test').fadeIn(3000);
				nEditing = null;
				orig_student_type = null;
			}
			else if ( nEditing == nRow && this.innerHTML == 'Add Student') {
				/* Editing this row and want to save it */
				saveRow( studentTable, nEditing, 'Add' );
//				$('#test').fadeIn(3000);
				nEditing = null;
			}
			else {
				/* No edit in progress - lets start one */
				editRow( studentTable, nRow, false );
//				$('#test').fadeOut(3000);
				nEditing = nRow;
			}
		} );

		function StudentType_change()
		{
			if (orig_student_type != $('select.student_types option:selected')[0].id) {
//				alert('Please make sure you have un-enroled this student from the blocks from the previous type: '+orig_student_name);
				
				var msg = 'This student will be un-enrolled from all ';
				msg    += orig_student_name+' courses if you save the ';
				msg    += 'students details with a different student type.';
				msg    += orig_student_type+'.';
				msg    += $('select.student_types')[0].selectedOptions[0].id;
				if (confirm(msg)) {
//					alert('ok');
					var nRow = $(this).parents('tr')[0];
					var jqInputs  = $('input', nRow);
					var request = $.ajax( {
						url: 'unenrol_student_from_all_blocks.php',
						type: 'POST',
						data: { StudentID  :  jqInputs[0].value },
						dataType: 'html'
					} );

					request.done(function(msg) {
						$('#debug').html( msg);
					});

					request.fail(function(jqXHR, textStatus) {
					  alert( 'Request failed: ' + textStatus );
					});
				} else {
				}
			}
		}

		function StudentType_focus()
		{
			if (orig_student_type == null) {
				orig_student_type = $('select.student_types option:selected')[0].id;
				orig_student_name = $('select.student_types option:selected')[0].text;
			}
		}
		
		$('select.student_types').live( 'focus', StudentType_focus );
		$('select.student_types').live('change', StudentType_change );
	} );
");

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
	   </div>
      </td>
     </tr>
    </table>
   </div>
   
   <div id="debug" class="debug"></div>
   <div id="test">
    <iframe frameborder="0" style="width: 79%; height: 480px;" id="students_blocks"></iframe>
    <iframe frameborder="0" style="width: 19%; height: 420px; float: right;" id="average_results"></iframe>
   </div>

<?php
print_footer();
?>

