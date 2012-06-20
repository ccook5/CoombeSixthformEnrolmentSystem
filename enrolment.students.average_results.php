<?php
require_once('config.inc.php');
require_once('header.inc.php');

print_header($title = 'Coombe Sixth form enrolment form.', $hide_title_bar = true, $script = "

	$(document).ready(function()
	{
	});
	
	", $exclude_datatables_js = false);

if (! isset($_GET['StudentID'])) {
	echo "<div class='error'>No student Id found</div>";
	die;
} else {
	$StudentID = mysql_real_escape_string($_GET['StudentID']);
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
  <table class="average_results">
   <tr>
    <td>Total Points: <?php echo $total_points; ?></td>
    <td>Equivalient GCSEs: <?php echo $equivalent_gcses; ?></td>
   </tr>
   <tr>
<?php if ($equivalent_gcses == 0) { ?>
    <td colspan="2">No Results Found.</td>
<?php } else { ?>
    <td colspan="2">Average Score: <?php echo(round($total_points/$equivalent_gcses, 1)); ?></td>
<?php } ?>
   </tr>		
  </table>

  <input type="button" value="Recalulate" onClick="window.location.reload()">
 </body>
</html>