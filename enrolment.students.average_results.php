<?php
require_once('config.inc.php');
require_once('header.inc.php');

print_header($title = 'Coombe Sixth form enrolment form.', $hide_title_bar = true, $script = "
	$(document).ready(function()
	{
		setInterval(function(){
			location.reload();
		}, 30000);
	});
	");

if (! isset($_GET['StudentID'])) {
	die( "<div class='error'>No student Id found</div>");
} else {
	$StudentID = mysql_real_escape_string($_GET['StudentID']);
}

/** Takes a Subject Name (as text) and returns a score.
 */
function get_result($Subject)
{
	global $StudentID, $link;
	
	$sql  = "SELECT GCSE_Grade.Grade FROM GCSE_Results";
	$sql .= " INNER JOIN GCSE_Subjects ON GCSE_Results.SubjectID=GCSE_Subjects.id";
	$sql .= " INNER JOIN GCSE_Grade ON GCSE_Results.GradeID=GCSE_Grade.id";
	$sql .= " INNER JOIN GCSE_Qualification ON GCSE_Grade.QualificationID=GCSE_Qualification.id";
	$sql .= " WHERE GCSE_Subjects.Name='".$Subject."' AND GCSE_Results.StudentID='".$StudentID."' LIMIT 1;";
	$result = mysql_query($sql, $link);

	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	if (mysql_num_rows($result) < 1) {
		return "Not found";
	} else if (mysql_num_rows($result) > 1) {
		return "Too many found";
	}

	return mysql_result($result, 0);
}

$sql  = "SELECT * FROM GCSE_Results";
$sql .= " INNER JOIN GCSE_Grade ON GCSE_Results.GradeID=GCSE_Grade.id";
$sql .= " INNER JOIN GCSE_Qualification ON GCSE_Grade.QualificationID=GCSE_Qualification.id";
$sql .= " WHERE GCSE_Results.StudentID='".$StudentID."'";
$result = mysql_query($sql, $link);

$total_points     = 0.0;
$equivalent_gcses = 0.0;

if (!$result) {
	die('Invalid query: ' . mysql_error());
} else {
	$first_option = true;
	while($row = mysql_fetch_assoc($result)) {
		$total_points     += $row["Points"];
		$equivalent_gcses += $row["EquivalentGCSE"];
	}
}
?>

  <div class='average_results' style='margin-top: 0px;'>
   <table class='average_results' cellspacing=0>
    <tr><td colspan='2'>GCSE GRADES</td></tr>
	
    <tr><td class='result-label'>English Lang.:</td>    <td class='result'><?php echo get_result('English Language'); ?></td></tr>
    <tr><td class='result-label'>Maths:</td>            <td class='result'><?php echo get_result('Maths'); ?></td></tr>
    <tr><td class='result-label'>Core Science:</td>     <td class='result'><?php echo get_result('Science Core'); ?></td></tr>
    <tr><td class='result-label'>Aditional Science:</td><td class='result'><?php echo get_result('Science Additional'); ?></td></tr>
    <tr><td class='result-label'>Chemistry:</td>        <td class='result'><?php echo get_result('Chemistry'); ?></td></tr>
    <tr><td class='result-label'>Biology:</td>          <td class='result'><?php echo get_result('Biology'); ?></td></tr>
    <tr><td class='result-label'>Physics:</td>          <td class='result'><?php echo get_result('Physics'); ?></td></tr>
   <tr>
    <td>Total Points: <?php echo $total_points; ?></td>
    <td>Equivalient GCSEs: <?php echo $equivalent_gcses; ?></td>
   </tr>
   <tr>
    <td colspan="2">
<?php
	if ($equivalent_gcses == 0) {
		echo("No Results Found.\n");
    } else {
        echo("<b>Average GCSE Score: ".round($total_points/$equivalent_gcses, 1)."</b>");
    }
?>
    </td>
   </tr>	
   </table>
  </div>

  <input type="button" value="Recalulate" onClick="window.location.reload()">
 </body>
</html>