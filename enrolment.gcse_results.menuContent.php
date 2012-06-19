<?php

require_once 'config.inc.php';

function print_qualifications_menu()
{
	$sql_qualification    = "SELECT * FROM GCSE_Qualification";
	$result_qualification = mysql_query($sql_qualification, $link);

	if (!$result_qualification) {
		die('Invalid query: ' . mysql_error());
	} else {
		echo "<ul id='accordion'>\n";

		while($row_qualification = mysql_fetch_array($result_qualification))  {
			echo "			<li>".$row_qualification["Type"].' ('.$row_qualification["Length"].")</li>\n";
		
			$sql_grade = "SELECT * FROM GCSE_Grade WHERE QualificationID=".$row_qualification['id'];

			$result_grade = mysql_query($sql_grade, $link);
			if (!$result_grade) {
				die('Invalid query: ' . mysql_error());
			} else {
				echo "<ul>\n";

				while($row_grade = mysql_fetch_array($result_grade))  {
					echo "			<li>".$row_grade["Grade"].' ('.$row_grade["Points"].")</li>\n";
				}
				
				echo "</ul>\n";
			}
		}

		echo "</ul>\n";
	}
}

?>