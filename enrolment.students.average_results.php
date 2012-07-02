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
	echo "<div class='error'>No student Id found</div>";
	die;
} else {
	$StudentID = mysql_real_escape_string($_GET['StudentID']);
}

function get_result($Subject)
{
	global $StudentID, $link;
	
	$sql    = "SELECT * FROM GCSE_Subjects WHERE Name=\"".$Subject."\" LIMIT 1";
	$result = mysql_query($sql, $link);

	$score = 0;

	$SubjectID = 0;
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	} else {
		$SubjectID = mysql_result($result, 0);
	}
	
	
	$sql    = "SELECT GradeID FROM GCSE_Results WHERE StudentID=\"".$StudentID."\" AND SubjectID=\"".$SubjectID."\" LIMIT 1";
	$result = mysql_query($sql, $link);

	$GradeID = 0;
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	} else {
		if (mysql_num_rows($result) != 1) {
			return "Not found";
		} else {
			$GradeID = mysql_result($result, 0);
		}
	}
	
	$sql = "SELECT Grade FROM GCSE_Grade WHERE id=".$GradeID." LIMIT 1";
	$result = mysql_query($sql, $link);
	
	$score = 0;

	if (!$result) {
		die('Invalid query: ' . mysql_error());
	} else {
		$score = mysql_result($result, 0);
	}
	
	return $score;
}

$sql = "SELECT * FROM GCSE_Results INNER JOIN GCSE_Grade ON GCSE_Results.GradeID=GCSE_Grade.id INNER JOIN GCSE_Qualification ON GCSE_Grade.QualificationID=GCSE_Qualification.id WHERE GCSE_Results.StudentID='".$StudentID."'";
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

   <div class='block' style=' margin-top: 0px;'>
   <table class='with-borders-horizontal'>
    <tr><td colspan='4' stlye='text-align: center;'>GCSE GRADES</td></tr>
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
<?php if ($equivalent_gcses == 0) { ?>
    <td colspan="2">No Results Found.</td>
<?php } else { ?>
    <td colspan="2"><b>Average GCSE Score: <?php echo(round($total_points/$equivalent_gcses, 1)); ?></b></td>
<?php } ?>
   </tr>	
   </table>
  </div>

  <input type="button" value="Recalulate" onClick="window.location.reload()">
 </body>
</html>