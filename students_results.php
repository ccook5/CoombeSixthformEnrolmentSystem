<?php 
$debug = false;
require_once('config.inc.php');
require_once('header.inc.php');
require_once('footer.inc.php');

if (! isset($_GET['student_id'])) {
	die( "<div class='error'>No student Id found</div>" );
} else {
	$StudentID = mysql_real_escape_string($_GET['student_id']);
}

$hide_columns = "";

if ($config["debug"] != true) {
	$hide_columns = "				{ 'bVisible': false, 'aTargets': [ 0, 1 ] },\n";
}

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = true, $script = "
var StudentID = '".$StudentID."';

$(document).ready( function() {
	
	var ResultsTable = $('#results').dataTable( {
		'bProcessing': true,
		'sAjaxSource': 'get_results.php?StudentID=".$StudentID."',
		'sScrollY'   : '280px',
		'bFilter'    : false,
		'bPaginate'  : false,
		'fnRowCallback': function( nRow, aData, iDisplayIndex ) {
			$('td:eq(5)', nRow).html( '<input type=\"button\" class=\"edit\" value=\"Edit\" />' );
			$('td:eq(6)', nRow).html( '<input type=\"button\" class=\"delete\" value=\"Delete\" />' );
		},
		'aoColumnDefs': [".$hide_columns."
// Center the first 2 (id + student id), grade(third last) and last two columns (edit/delete buttons)
			{ 'sClass'  : 'center', 'aTargets': [ 0, 1, -1, -2, -3 ] },
			
// Minimise the width of the first two and last two columns (edit/delete buttons)
			{ 'sWidth'  : '5%', 'aTargets': [ 0, 1, -1, -2] }
		]
	} );

	students_results(ResultsTable);
} );
	", $exclude_datatables_js = false, $meta = "", $extra_script = "/students_results.js.php");
?>
   <div class='block' >
       <span><a id="new_result" href="">Add New Result</a></span>
       <div id="dynamic">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="results">
         <thead>
          <tr>
		    <th>ID</th>
			<th>StudentID</th>
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
	   </div><!-- class=dynamic -->
   </div><!-- class=block -->
   <div id="debug" class="debug"></div>
<?php
print_footer($show_links=false);
?>

