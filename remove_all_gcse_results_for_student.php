<?php

require_once('config.inc.php');
require_once('functions.inc.php');

if (isset($_POST['StudentID']))
{
	delete_all_results_for_student(get_post_val('StudentID'));
}
?>