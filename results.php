<?php 
require_once 'config.inc.php';

include 'header.inc.php';

function build_student_type_list()
{
	global $link;
	$student_types = "\n";
	$sql_student_types = "SELECT * from StudentTypes";
	$result_student_types = mysql_query($sql_student_types, $link);
	if (!$result_student_types) {
		die('Invalid query: ' . mysql_error());
	} else {
		while($row_student_types = mysql_fetch_array($result_student_types))  {
			$student_types .= "			student_types[".$row_student_types['id']."] = '".$row_student_types['CourseType']."';\n";
		}
	}
	return $student_types;
}

print_header($title = 'Coombe Sixth form enrolment form.', $hide_title_bar = false, $script = "
	$(document).ready(function() {
		function restoreRow ( oTable, nRow ) {
			var aData = oTable.fnGetData(nRow);
			var jqTds = $('>td', nRow);
			for ( var i=0, iLen=jqTds.length ; i<iLen ; i++ ) {
				oTable.fnUpdate( aData[i], nRow, i, false );
			}
			oTable.fnDraw();
		}
		function build_student_type_select( d ) {
			var student_types = Array();
			".build_student_type_list()."
			var s = '<select>';
			var iLen = 0;
			for (var i = 0, iLen=student_types.length; i<iLen; i++) {
			    s = s + '<option';
				if (d == student_types[i]) {
					s = s + ' selected=\"selected\"';
				}
				s = s + '>' + student_types[i] + '</option>';
			}
			s = s + '</select>';
			return s;
		}
		function editRow ( oTable, nRow ) {
			var aData = oTable.fnGetData(nRow);
			var jqTds = $('>td', nRow);
			jqTds[0].innerHTML = '<input type=\"text\" value=\"'+aData[0]+'\">';
			jqTds[1].innerHTML = '<input type=\"text\" value=\"'+aData[1]+'\">';
			jqTds[2].innerHTML = '<input type=\"text\" value=\"'+aData[2]+'\">';
			jqTds[3].innerHTML = '<input type=\"text\" value=\"'+aData[3]+'\">';
			jqTds[4].innerHTML = '<input type=\"text\" value=\"'+aData[4]+'\">';
			jqTds[5].innerHTML = build_student_type_select(aData[5]);
			jqTds[6].innerHTML = '<a class=\"edit\" href=\"\">Save</a>';
		}
		function saveRow ( oTable, nRow, action ) {
			var jqInputs = $('input', nRow);
			var jqSelects = $('select', nRow);
			var xmlhttp;
			if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			}
			else
			{// code for IE6, IE5
				xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
			}
			xmlhttp.open('POST', 'ajax_update_students.php', false);
			xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			
			if (action == 'Save') {
				xmlhttp.send('action=update&student_id='+jqInputs[0].value+'&FirstName='+jqInputs[1].value+'&LastName='+jqInputs[2].value+'&StudentType='+jqSelects[0].options[jqSelects[0].selectedIndex].value+'&PreviousInstitution='+jqInputs[3].value+'&EnrolmentYear='+jqInputs[4].value);
			} else {
				xmlhttp.send('action=new&student_id='+jqInputs[0].value+'&FirstName='+jqInputs[1].value+'&LastName='+jqInputs[2].value+'&StudentType='+jqSelects[0].options[jqSelects[0].selectedIndex].value+'&PreviousInstitution='+jqInputs[3].value+'&EnrolmentYear='+jqInputs[4].value);
			}
			document.getElementById('debug').innerHTML = xmlhttp.responseText;

			oTable.fnUpdate( jqInputs[0].value, nRow, 0, false );
			oTable.fnUpdate( jqInputs[1].value, nRow, 1, false );
			oTable.fnUpdate( jqInputs[2].value, nRow, 2, false );
			oTable.fnUpdate( jqInputs[3].value, nRow, 3, false );
			oTable.fnUpdate( jqInputs[4].value, nRow, 4, false );
			oTable.fnUpdate( jqSelects[0].options[jqSelects[0].selectedIndex].value, nRow, 5, false );
			
			oTable.fnUpdate( '<a class=\"edit\" href=\"\">Edit</a>', nRow, 6, false );
			oTable.fnDraw();
		}

		var nEditing = null;

		var studentTable = $('#students').dataTable( {
			'bProcessing': true,
			'sAjaxSource': 'get_students.php',
			'sScrollY'   : '200px',
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
			$('#students_results').attr('src','/students_results.php');
			$(studentTable.fnSettings().aoData).each(function (){
				$(this.nTr).removeClass('row_selected');
			});
			$(event.target.parentNode).addClass('row_selected');
			$('#students_results').attr('src','/students_results.php?student_id='+$(event.target.parentNode).find('td:first').html());
		}) ;
				
		$('#new_student').click( function (e) {
			e.preventDefault();
			
			var aiNew = studentTable.fnAddData( [ '', '', '', '', '', '',
				'<a class=\"edit\" href=\"\">Add</a>', '<a class=\"delete\" href=\"\">Delete</a>' ] );
			var nRow = studentTable.fnGetNodes( aiNew[0] );
			editRow( studentTable, nRow );
			nEditing = nRow;
		} );
		
		$('#students a.delete').live('click', function (e) {
			e.preventDefault();
			
			var nRow = $(this).parents('tr')[0];

			var jqInputs = $('input', nRow);
			
			xmlhttp.open('POST', 'ajax_update_student.asp', true);
			xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			xmlhttp.send('action=delete&student_id='+jqInputs[0]);
			
			studentTable.fnDeleteRow( nRow );
		} );
		
		$('#students a.edit').live('click', function (e) {
			e.preventDefault();
			
			/* Get the row as a parent of the link that was clicked on */
			var nRow = $(this).parents('tr')[0];
			
			if ( nEditing !== null && nEditing != nRow ) {
				/* Currently editing - but not this row - restore the old before continuing to edit mode */
				restoreRow( studentTable, nEditing );
				editRow( studentTable, nRow );
				nEditing = nRow;
			}
			else if ( nEditing == nRow && this.innerHTML == 'Save') {
				/* Editing this row and want to save it */
				saveRow( studentTable, nEditing, 'Save' );
				nEditing = null;
			}
			else if ( nEditing == nRow && this.innerHTML == 'Add') {
				/* Editing this row and want to save it */
				saveRow( studentTable, nEditing, 'Add' );
				nEditing = null;
			}
			else {
				/* No edit in progress - lets start one */
				editRow( studentTable, nRow );
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
       </td>
      </tr>
     </table>
   </div>
   <div id="debug"></div>

     <iframe frameborder=0 style="width: 100%; height: 350px;" id="students_results"></iframe>
 </body>
</html>