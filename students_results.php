<?php 
require_once 'config.inc.php';

include 'header.inc.php';


if (! isset($_GET['student_id'])) {
  echo "<div class='error'>No student Id found</div>";
  die;
} else {
  $student_id = mysql_real_escape_string($_GET['student_id']);
}

function build_list($sql, $list_name, $key_column, $value_column, $value_column2 = "")
{
	global $link;
	$list = "\n";
	$result = mysql_query($sql, $link);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	} else {
		while($row = mysql_fetch_array($result))  {
			if ($value_column2 == "") {
				$list .= "			".$list_name."[".$row[$key_column]."] = '".$row[$value_column]."';\n";
			} else {
				$list .= "			".$list_name."[".$row[$key_column]."] = '".$row[$value_column]." - ".$row[$value_column2]."';\n";
			}
		}
	}
	return $list;
}

function build_f_key_list($sql, $list_name, $key_column, $fk_column)
{
	global $link;
	$list = "\n";
	$result = mysql_query($sql, $link);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	} else {
		while($row = mysql_fetch_array($result))  {
			$list .= "			".$list_name."[".$row[$key_column]."] = '".$row[$fk_column]."';\n";
		}
	}
	return $list;
}

function create_select_builder($func_name, $sql, $class_name_test, $key_column, $value_column, $value_column2 = "", $f_key_column = "", $id_column = "")
{
	$s  = "\n";
	$s .= "		function ". $func_name ."( d ) {\n";
	$s .= "			var arr = Array();\n";
	$s .= "         ".build_list($sql, "arr", $key_column, $value_column, $value_column2)."\n";
	if ($f_key_column != "") {
		$s .= "			var f_keys = Array();\n";
		$s .= "         ".build_f_key_list($sql, "f_keys", $key_column, $f_key_column)."\n";
	}
	$s .= "			var s = '<select class=\"".$class_name_test."\">';\n";
	$s .= "			var iLen = 0;\n";
	$s .= "			for (var i = 1, iLen=arr.length; i<iLen; i++) {\n";
	$s .= "				s = s + '<span>';\n";
	$s .= "				s = s + '<option';\n";
	$s .= "				s = s + ' class=\"".$class_name_test."\"';\n";
	$s .= "				s = s + ' id='+i+''\n";
	if ($f_key_column != "") {
		$s .= "				s = s +' value='+ f_keys[i];";
	}
	$s .= "				if (d == arr[i]) {\n";
	$s .= "					s = s + ' selected=\"selected\"';\n";
	$s .= "				}\n";
	$s .= "				s = s + '>' + arr[i] + '</option>';\n";
	$s .= "				s = s + '</span>';\n";
	$s .= "			}\n";
	$s .= "			s = s + '</select>';\n";
	$s .= "			return s;\n";
	$s .= "		}\n";

	return $s;
}

print_header($title = 'Coombe Sixth form registration form.', $hide_title_bar = true, $script = "
	$(document).ready(function() {
		function restoreRow ( oTable, nRow ) {
			var aData = oTable.fnGetData(nRow);
			var jqTds = $('>td', nRow);
			for ( var i=0, iLen=jqTds.length ; i<iLen ; i++ ) {
				oTable.fnUpdate( aData[i], nRow, i, false );
			}
			oTable.fnDraw();
		}".
//		create_select_builder('build_student_type_select',  "SELECT * from StudentTypes",       'student_type', 'id', 'CourseType').
		create_select_builder('build_GCSE_Type_select',     "SELECT * from GCSE_Qualification", 'gcse_type',    'id', 'Type', 'Length').
		create_select_builder('build_subject_names_select', "SELECT * from GCSE_Subjects",      'subject_name', 'id', 'Name').
		create_select_builder('build_GCSE_Grade_select',    "SELECT * FROM GCSE_Grade ORDER BY Points",         'gcse_grade',   'id', 'Grade', 'Points', 'QualificationID').
		"
		function editRow ( oTable, nRow ) {
			var aData = oTable.fnGetData(nRow);
			var jqTds = $('>td', nRow);
			jqTds[0].innerHTML = build_GCSE_Type_select    (aData[0]);
			jqTds[1].innerHTML = build_subject_names_select(aData[1]);
			jqTds[2].innerHTML = build_GCSE_Grade_select   (aData[2]);
			jqTds[4].innerHTML = '<a class=\"edit\" href=\"\">Save</a>';
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
			xmlhttp.open('POST', 'ajax_update_students_results.php', false);
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
			oTable.fnUpdate( jqSelects[0].options[jqSelects[0].selectedIndex].value, nRow, 3, false );
			
			oTable.fnUpdate( '<a class=\"edit\" href=\"\">Edit</a>', nRow, 5, false );
			oTable.fnDraw();
		}

		var nEditing = null;

		var studentTable = $('#students').dataTable( {
			'bProcessing': true,
			'sAjaxSource': 'get_results.php?student_id=".$student_id."',
			'sScrollY'   : '200px',
			'bFilter'    : false,
			'bPaginate'  : false,
			'fnRowCallback': function( nRow, aData, iDisplayIndex ) {
				$('td:eq(3)', nRow).html( '<a class=\"edit\" href=\"\">Edit</a>' );
				$('td:eq(4)', nRow).html( '<a class=\"delete\" href=\"\">Delete</a>' );
			}
			//'aoColumnDefs': [ {
			///	'sClass'  : 'center',
			//	'aTargets': [ -1, -2 ]
			//} ]
		} );
		
		/* Add a click handler to the rows - this could be used as a callback */
		$('#students tbody').click( function( event ) {
		
			$(studentTable.fnSettings().aoData).each(function (){
				$(this.nTr).removeClass('row_selected');
			});
			$(event.target.parentNode).addClass('row_selected');
		}) ;
				
		$('#new_student').click( function (e) {
			e.preventDefault();
			
			var aiNew = studentTable.fnAddData( [ '', '', '',
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
		$('select.gcse_type').live('change', function() {
		
			$('option.gcse_grade').each( function(index) {
			
				if (this.value == $('select.gcse_type option:selected').attr('id')) { 
		//			alert(this.parentElement.nodeName);
					if (this.parentElement.nodeName == 'SPAN') {
						$(this).unwrap();
					}
				} else {
					$(this).wrap('<span style=\"display: none\" />');
				}
				
			});
		});
	} );
	");
?>

   <div class='block' >
    <table class='with-borders-horizontal'>
     <tr >
      <td>
       <p><a id="new_student" href="">Add New Grade</a></p>
       <div id="dynamic">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="students">
         <thead>
          <tr>
			<th>Type</th>
			<th>Subject</th>
			<th>Grade</th>
			<th>Edit</th>
			<th>Delete</th>
          </tr>
         </thead>
         <tbody>
          <tr>
           <td colspan="5" class="dataTables_empty">Loading data from server</td>
          </tr>
         </tbody>
        </table>
       </td>
      </tr>
     </table>
   </div>
 </body>
</html>