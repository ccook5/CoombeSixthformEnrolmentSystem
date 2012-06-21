<?php 
$debug = false;
require_once 'config.inc.php';
include      'header.inc.php';

if (! isset($_GET['student_id'])) {
	echo "<div class='error'>No student Id found</div>";
	die;
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
		'sScrollY'   : '200px',
		'bFilter'    : false,
		'bPaginate'  : false,
		'aoColumnDefs': [".$hide_columns."
// Center the first 2 (id + student id), grade(third last) and last two columns (edit/delete buttons)
			{ 'sClass'  : 'center', 'aTargets': [ 0, 1, -1, -2, -3 ] },
			
// Minimise the width of the first two and last two columns (edit/delete buttons)
			{ 'sWidth'  : '5%', 'aTargets': [ 0, 1, -1, -2] }
		]
	} );

	students_results(ResultsTable);
}
	
);

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
       </td>
      </tr>
     </table>
   </div>
   <div id="debug"></div>
 </body>
</html>