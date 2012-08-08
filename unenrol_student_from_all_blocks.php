<?php

require_once('config.inc.php');
require_once('functions.inc.php');

if (isset($_POST['StudentID']))
{
	delete_all_courseenrolments_for_student(get_post_val('StudentID'));
}
?>