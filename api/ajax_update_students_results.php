<?php

require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');
require_once('../functions.inc.php');

function print_html_select($sql, $column_to_show, 
							$select_id = "",    $select_name="", 
							$select_class = "", $select_style = "", 
							$second_column_to_show = "", $option_id_column = "")
{
	global $link;
	echo "      <select ";
	if ($select_name  != "") echo "name='" .$select_name ."' ";
	if ($select_id    != "") echo "id='"   .$select_id   ."' ";
	if ($select_class != "") echo "class='".$select_class."' ";
	if ($select_style != "") echo "style='".$select_style."' ";
    echo " >\n";

	$result = mysql_query($sql, $link);

	if (!$result) {
		die('Invalid query: ' . mysql_error());
	} else {
			$first_option = true;
			while($row = mysql_fetch_assoc($result)) {
				print("       <option ");
				if ($option_id_column != "") { print(" id=\""      .$row[$option_id_column]."\" "); }
				if ($select_class     != "") { print(" class=\""   .$select_class          ."\" "); }
				if ($option_id_column != "") { print(" value=\""   .$row[$option_id_column]."\" "); }
				if ($first_option)           { print(" selected "); $first_option = false; }
				print(">");
				if ($second_column_to_show == "") {
					print($row[$column_to_show]);
				} else {
					print($row[$column_to_show]." - ".$row[$second_column_to_show]);
				}
				print("</option>\n");
			}
	}
	echo "     </select>\n";
}

if (!isset($_POST['action'])) {

print_header($title = 'Coombe Sixth form enrolment form.', $hide_title_bar = true, $script = "

	$(document).ready(function()
	{
		update_grade_selectbox();
	});
	
	$('select.gcse_type').live('change', update_grade_selectbox );", $exclude_datatables_js = false);
?>
  <h4>ajax_update_students_results.php tester</h4>
  <form action="api/ajax_update_students_results.php" method="post"> 
   <table style='width: 300px; border: 1px solid black;' >
    <tr>
     <td>Action</td>
     <td>
      <select name='action' > 
       <option>delete</option><option>new</option><option>update</option>
      </select>
     </td>
    </tr>
    <tr>
     <td>Student ID</td>
     <td><input type='text' name='StudentID' /></td>
    </tr>
    <tr>
     <td>Result ID</td>
     <td><input type='text' name='ResultID' /></td>
    </tr>
    <tr>
     <td>Subject</td>
     <td>
	  <?php print_html_select("SELECT * FROM GCSE_Subjects", $column_to_show="Name", $select_id="SubjectID", $select_name="SubjectID", $select_class="subject_name", "", "", $option_id_column = "id"); ?>
     </td>
    </tr>
    <tr>
     <td>Result Type</td>
     <td>
	  <?php print_html_select("SELECT * FROM GCSE_Qualification", $column_to_show="Type", $select_id="gcse_type", $select_name="", $select_class="gcse_type", "", $second_column_to_show = "Length", $option_id_column = "id"); ?>
     </td>
    </tr>
    <tr>
     <td>Grade</td>
     <td>
	  <?php print_html_select("SELECT * FROM GCSE_Grade RIGHT JOIN GCSE_Qualification ON GCSE_Grade.QualificationID=GCSE_Qualification.id", 
															$column_to_show = "Grade", $select_id="gcse_grade", $select_name="GradeID", $select_class="gcse_grade", "", $second_column_to_show = "Points", $option_id_column = "QualificationID"); ?>
     </td>
    </tr>
    <tr><td><input type="submit" /></td></tr>
   </table>
  </form>
<?php
	print_footer();
} else {
	$action = get_post_val('action');
	$ResultID = get_post_val('ResultID');

	print("action = ".$action);
	
	if ($action == "delete") {
	//We have all the info we need
	}
	else if ($action == "update" or $action == "new") {
		$StudentID       = get_post_val('StudentID');
		$SubjectID       = get_post_val('SubjectID');
		$GradeID         = get_post_val('GradeID');
		
		print("<p>&dollar;StudentID = ".$StudentID."</p>");
		print("<p>&dollar;SubjectID = ".$SubjectID."</p>");
		print("<p>&dollar;GradeID   = ".$GradeID."</p>");
	}
	else {
		print("<div class='error'>Error: Incorrect Action</div>");
	}
	
	if ($action == "delete") {
		$sql = "DELETE FROM GCSE_Results WHERE id='".$ResultID."'";
	}
	else if ($action == "new")
	{
		$sql = "INSERT INTO GCSE_Results (SubjectID, GradeID, StudentID, EnrolmentYear) VALUES ('".$SubjectID."', '".$GradeID."', '".$StudentID."', '".$config['current_year']."')";
	}
	else if ($action == "update")
	{
		$sql = "UPDATE GCSE_Results SET SubjectID='".$SubjectID."', GradeID='".$GradeID."', StudentID='".$StudentID."' WHERE id='".$ResultID."'";
	}

	$result = mysql_query($sql, $link);

	if (!$result)
	{
		print('sql:'.$sql);
		die('Invalid query: ' . mysql_error());
	}
}
?>
