<?php 

require_once('config.inc.php');
require_once('header.inc.php');
require_once('footer.inc.php');

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = false, $script = "");

?>
  <ul>
   <li><a href="/reports/courses.php">View all course instances and enrolled students.</a></li>
   <li><a href="/reports/students.php">View all students and their courses</a></li>
   <li><a href="/reports/waiting.php">View subject waiting lists</a></li>
   <li><a href="/reports/remaining.php">View places remaining.</a></li>
   <br />
   <li><a href="/reports/next_enrolee.php">Next Student<a></li>
  </ul>
  
<?php print_footer(); ?>
