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
	$s .= "			for (var i = 1; i<arr.length; i++)\n";
	$s .= "			{\n";
	$s .= "				s += '<span>';\n";
	$s .= "				s += ' <option';\n";
	$s .= "				s += '  class=\"".$class_name_test."\"';\n";
	$s .= "				s += '  id='+i+''\n";
	if ($f_key_column != "") {
		$s .= "				s += ' value=' + f_keys[i];\n";
	}
	$s .= "				if (d == arr[i]) {\n";
	$s .= "					s += ' selected=\"selected\"';\n";
	$s .= "				}\n";
	$s .= "				s += '>' + arr[i] + '</option>';\n";
	$s .= "				s += ' </span>';\n";
	$s .= "			}\n";
	$s .= "			s += '</select>';\n";
	$s .= "			return s;\n";
	$s .= "		}\n";

	return $s;
}

print_header($title = 'Coombe Sixth form enrolment form.', $hide_title_bar = true, $script = "
	$(document).ready(function() {
		function restoreRow ( oTable, nRow ) {
			var aData = oTable.fnGetData(nRow);
			var jqTds = $('>td', nRow);
			for ( var i=0, iLen=jqTds.length ; i<iLen ; i++ ) {
				oTable.fnUpdate( aData[i], nRow, i, false );
			}
			oTable.fnDraw();
		}".
		create_select_builder('build_GCSE_Type_select',     "SELECT * from GCSE_Qualification",         'gcse_type',    'id', 'Type', 'Length').
		create_select_builder('build_subject_names_select', "SELECT * from GCSE_Subjects",              'subject_name', 'id', 'Name').
		create_select_builder('build_GCSE_Grade_select',    "SELECT * FROM GCSE_Grade ORDER BY Points", 'gcse_grade',   'id', 'Grade', ''/*'Points'*/, 'QualificationID').
		"
		function editRow ( oTable, nRow )
		{
			var aData = oTable.fnGetData(nRow);
			var jqTds = $('>td', nRow);
			//skip column 0
			jqTds[1].innerHTML = build_GCSE_Type_select    (aData[1]);
			jqTds[2].innerHTML = build_subject_names_select(aData[2]);
			jqTds[3].innerHTML = build_GCSE_Grade_select   (aData[3]);
			jqTds[4].innerHTML = '<a class=\"edit\" href=\"\">Save</a>';
			update_grade_selectbox();
		}
		
		function saveRow ( oTable, nRow, action )
		{
			var jqSelects = $('select', nRow);
			var jqTds = $('>td', nRow);
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
			
			http_data  = 'student_id=".$student_id."';
			http_data += '&ResultID='  + jqTds[0].innerHTML;
			http_data += '&SubjectID=' + jqSelects[1].options[jqSelects[1].selectedIndex].id;
			http_data += '&GradeID=' + jqSelects[2].options[jqSelects[2].selectedIndex].id;

			if (action == 'Save') {
				http_data  = 'action=update&' + http_data;
				xmlhttp.send(http_data);
			} else {
				http_data  = 'action=new&' + http_data;
				xmlhttp.send(http_data);
			}
			document.getElementById('debug').innerHTML = xmlhttp.responseText;

			oTable.fnUpdate( jqSelects[0].options[jqSelects[0].selectedIndex].text, nRow, 1, false );
			oTable.fnUpdate( jqSelects[1].options[jqSelects[1].selectedIndex].text, nRow, 2, false );
			oTable.fnUpdate( jqSelects[2].options[jqSelects[2].selectedIndex].text, nRow, 3, false );
			
			oTable.fnUpdate( '<a class=\"edit\" href=\"\">Edit</a>', nRow, 4, false );
			oTable.fnDraw();
		}

		var nEditing = null;

		var ResultsTable = $('#results').dataTable( {
			'bProcessing': true,
			'sAjaxSource': 'get_results.php?student_id=".$student_id."',
			'sScrollY'   : '200px',
			'bFilter'    : false,
			'bPaginate'  : false,
			'fnRowCallback': function( nRow, aData, iDisplayIndex ) {
				$('td:eq(4)', nRow).html( '<a class=\"edit\" href=\"\">Edit</a>' );
				$('td:eq(5)', nRow).html( '<a class=\"delete\" href=\"\">Delete</a>' );
			},
			'aoColumnDefs': [
// uncomment for production use?
//				{ 'bVisible': false, 'aTargets': [ 0 ] },

// Center the first (id), grade(third last) and last two columns (edit/delete buttons)
				{ 'sClass'  : 'center', 'aTargets': [ 0, -1, -2, -3 ] },
				
// Minimise the width of the first and last two columns (edit/delete buttons)
				{ 'sWidth'  : '5%', 'aTargets': [ 0, -1, -2] },
			]
		} );
		
		/* Add a click handler to the rows.
		   This adds a highlight to the currently selected row. This could 
		   also be used as a callback to do something with the row.
		*/
		$('#results tbody').click( function( event )
		{
			$(ResultsTable.fnSettings().aoData).each( function ()
			{
				$(this.nTr).removeClass('row_selected');
			});
			$(event.target.parentNode).addClass('row_selected');
		}) ;

		$('#new_result').click( function (e)
		{
			e.preventDefault();
			
			var aiNew = ResultsTable.fnAddData( ['', '', '', '',
				'<a class=\"edit\" href=\"\">Add</a>',
				'<a class=\"delete\" href=\"\">Delete</a>' ] );
			var nRow = ResultsTable.fnGetNodes( aiNew[0] );
			editRow( ResultsTable, nRow );
			nEditing = nRow;
			update_grade_selectbox();
		} );
		
		$('#results a.delete').live('click', function (e)
		{
			e.preventDefault();
			
			var nRow     = $(this).parents('tr')[0];
			var jqTds    = $('>td', nRow);
			var jqInputs = $('input', nRow);
			
			xmlhttp.open('POST', 'ajax_update_student.asp', true);
			xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			xmlhttp.send('action=delete&result_id='+jqTds[0]);
			
			studentTable.fnDeleteRow( nRow );
		} );
		
		$('#Results a.edit').live('click', function (e) {
			e.preventDefault();
			
			/* Get the row as a parent of the link that was clicked on */
			var nRow = $(this).parents('tr')[0];
			
			if ( nEditing !== null && nEditing != nRow ) {
				/* Currently editing - but not this row - restore the old before continuing to edit mode */
				restoreRow( ResultsTable, nEditing );
				editRow( ResultsTable, nRow );
				nEditing = nRow;
			}
			else if ( nEditing == nRow && this.innerHTML == 'Save') {
				/* Editing this row and want to save it */
				saveRow( ResultsTable, nEditing, 'Save' );
				nEditing = null;
			}
			else if ( nEditing == nRow && this.innerHTML == 'Add') {
				/* Editing this row and want to save it */
				saveRow( ResultsTable, nEditing, 'Add' );
				nEditing = null;
			}
			else {
				/* No edit in progress - lets start one */
				editRow( ResultsTable, nRow );
				nEditing = nRow;
			}
		} );
		
		function update_grade_selectbox()
		{
			$('option.gcse_grade').each( function(index) {
				
				if (this.value != $('select.gcse_type option:selected')[0].id) { 
					if (this.parentElement.nodeName != 'SPAN') {
						$(this).wrap('<span style=\"none\" />');
					}
				} else { 
					if (this.parentElement.nodeName == 'SPAN') {
						$(this).unwrap();
					}
				}
			});
		};
		
		$('select.gcse_type').live('change', update_grade_selectbox );
	} );
	");
?>

   <div class='block' >
    <table class='with-borders-horizontal'>
     <tr >
      <td>
       <p><a id="new_result" href="">Add New Result</a></p>
       <div id="dynamic">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="results">
         <thead>
          <tr>
		    <th>ID</th>
			<th>Type</th>
			<th>Subject</th>
			<th>Grade</th>
			<th>Edit</th>
			<th>Delete</th>
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
   <div id="debug"></div>
 </body>
</html>