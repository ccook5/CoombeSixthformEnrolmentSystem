<?php 

require_once('../config.inc.php');
require_once('../header.inc.php');

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = true, $script = "",
			$exclude_datatables_js = true, 
			$meta                  ="      <meta http-equiv='refresh' content='1;url=/reports/next_enrolee.php'/>");
?>

<h1><?php echo($config['next_enrolee']); ?>

</body>
</html>