<?php
require_once('config.inc.php');
require_once('header.inc.php');
require_once('footer.inc.php');
require_once('functions.inc.php');

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = true, $script = "

function clear_column(ColumnID)
{
	$('input[name=\'block['+ColumnID+']\' ]').attr(\"checked\", false);
}
 ");
 
if (! isset($_GET['student_id']) || $_GET['student_id'] == "null") {
	die( "<div class='error'>No student Id found</div>" );
} else {
	$StudentID = mysql_real_escape_string($_GET['student_id']);
}

$hide_columns = "";

if ($config["debug"] != true) {
	$hide_columns = "				{ 'bVisible': false, 'aTargets': [ 0, 1 ] },\n";
}

function print_coursetype_selects($StudentType)
{
	global $config, $link;
	$sql = "SELECT * FROM StudentTypes";
	$result = mysql_query($sql, $link);

	if (!$result) {  die('Invalid query: ' . mysql_error());  }
	else
	{
		while($row = mysql_fetch_array($result))
		{
			if ($StudentType == $row['id']) {
				echo "         <td>Student Type: </td><td>".$row['CourseType']."\n";
				echo "         <input type=\"hidden\" name=\"CourseTypeID\" value=\"".$StudentType."\" /></td>";
			}
		}
	}
	return 0;
}
	
function get_places_taken($courseID)
{
	global $config, $link;
	$sql_places    = "SELECT * FROM BLOCKS_CourseEnrolment WHERE EnrolmentYear=".$config['current_year']." AND CourseID='".$courseID."' ORDER BY id";
	$result_places = mysql_query($sql_places, $link);

	if ($result_places) 
	{
		return mysql_num_rows($result_places);
	}
	
	die('Invalid query: ' . mysql_error());
	return 0;
}

/** Get a list of all the students current enrolments.
  *
  * There may be none or more.
  *
  * There should only be one per block, but we don't check this currently.
  */
function get_students_current_enrolments($StudentID)
{
	global $config, $link;
	
	$sql_enrolments  = "SELECT * FROM BLOCKS_CourseEnrolment INNER JOIN BLOCKS_Course ON CourseID=BLOCKS_Course.id";
	$sql_enrolments .= " INNER JOIN BLOCKS_CourseDef ON BLOCKS_Course.CourseDefID=BLOCKS_CourseDef.id";
	$sql_enrolments .= " WHERE BLOCKS_CourseEnrolment.StudentID='".$StudentID."'";

	$result_enrolments = mysql_query($sql_enrolments, $link);

	$enrolments = Array();

	if ($result_enrolments)
	{
		while($row_enrolments = mysql_fetch_array($result_enrolments))
		{
			$enrolments[ $row_enrolments['BlockID'] ] = $row_enrolments['CourseID'];
		}
	    return $enrolments;
	}
	die('Invalid query: ' . mysql_error());
}

/** Print the html table for the students enrolments.
  *
  * We use get_students_current_enrolments() to get a list of what they have allready.
  * This table contains form elements that should let us submit changes to the enrolments.
  * This form should post the data to /api/blocks_handler.php
  */
function print_blocks_table($StudentID, $StudentType)
{
	global $config, $link;
	
	$enrolments         = get_students_current_enrolments($StudentID);
	
	// Get a list of the current blocks from the database. This should be a list like 'a','b','c',etc.
	$sql_blocks    = "SELECT * FROM BLOCKS_Blocks WHERE Year=".$config['current_year']." AND CourseType='".$StudentType."' ORDER BY id";
	$result_blocks = mysql_query($sql_blocks, $link);

//	echo("         <table class='with-borders-horizontal'>\n");
	echo("          <tr>\n");

	if ($result_blocks)
	{
		while($row_blocks = mysql_fetch_array($result_blocks))
		{
		    echo ("           <td style='padding: 0px; margin: 0px; padding-bottom: 30px; position: relative;'>\n");
			echo ("            <table class='with-borders'>\n");
			echo ("             <thead><th width='10%'>".$row_blocks['Name']."</th></thead>\n");
			
			/** Get all the current courses for the current block.
			  */
			$sql    = "SELECT * FROM BLOCKS_Course INNER JOIN BLOCKS_CourseDef ON CourseDefID=BLOCKS_CourseDef.id";
//			$sql   .= " INNER JOIN StudentTypes ON Type=StudentTypes.id";
			$sql   .= " WHERE EnrolmentYear=".$config['current_year']." AND BlockID=".$row_blocks['id'];
			$result = mysql_query($sql, $link);

			if ($result) {
				$row_count = 0;
				while($row = mysql_fetch_array($result))
				{
					$checked = "";
					// if student is allready enrolled on this course, then mark it as selected.

					if (isset($enrolments[ $row['BlockID'] ]) && $enrolments[ $row['BlockID'] ] == $row[0])
					{
						$checked = " checked ";
					}
					$places_taken = get_places_taken($row[0]);
					$max_pupils   = $row['MaxPupils'];
					$bgcolour     = "";
					
					if ( $max_pupils - $places_taken < 2 ) {
						$bgcolour = "background: red;";
					} else if ($max_pupils - $places_taken <  5 ) {
						$bgcolour = "background: orange;";
					}
?>
              <tr>
               <td style='height: 2.3em; <?php echo $bgcolour; ?>;'>
                <input 
                  type='radio' 
                  name='block[<?php echo $row_blocks['id']; ?>]' 
    	          id='block[<?php echo $row_blocks['id']; ?>][<?php echo $row_count; ?>]'
                  value='<?php echo $row[0]; ?>'
                  <?php echo $checked; ?> /> 
                <label class='blocks-label' for='block[<?php echo $row_blocks['id']; ?>][<?php echo $row_count;?>]'>
                  <span><?php echo $row['SubjectName']; ?></span>		
                </label>
                  <div class='places_left' >
                   <?php echo '('.$places_taken."/".$max_pupils.")\n"; ?>
                  </div>	
               </td>
              </tr>
<?php		
					$row_count ++;
				}
?>
             </table>
				 
             <input type='button'
              onClick='clear_column("<?php echo $row_blocks['id']; ?>")'
              value='Clear Block' 
              class='clear_block-button' 
             />
<?php
			} else {  die('Invalid query: ' . mysql_error());  }
		}
	} else {  die('Invalid query: ' . mysql_error());  }
		
	echo("             </tr>\n");
//	echo("            </table>\n");
}

$sql = "SELECT * FROM students WHERE id=\"".$StudentID."\" AND EnrolmentYear=\"".$config['current_year']."\"";
$result = mysql_query($sql, $link);

$row = 0;
if (!$result) {  die('Invalid query: ' . mysql_error());  }
else if (mysql_num_rows($result) > 0)
{
	$i = 0;
	$row = mysql_fetch_array($result);
?>
   <div class='block' >
    <form action="api/blocks_handler.php" method="post">
     <input type="hidden" name="StudentID" value="<?php echo $StudentID; ?>" />
     <div id="dynamic">
      <table>
       <tr>
 <!--       <td>Mobile Number:   <input type='text' name='mobile_number' value='<?php echo $row['MobileNumber'];   ?>' /></td> -->
        <td>Sequence Number:</td><td><input type='text' name='sequence_number' value='<?php echo $row['SequenceNumber']; ?>' /></td>
<?php print_coursetype_selects($row['StudentType']); ?>
       </tr>
<!--       <tr>
        <td colspan="6" style="align: center"> -->
<?php print_blocks_table($StudentID, $row['StudentType']); ?>
<!--        </td>
       </tr> -->
       <tr>
        <td colspan="3" style="align: center">
         <input type="submit" value="Save"/> - <input type="reset" />
        </td>
       </tr>
      </table>
     </div><!-- id=dynamic //-->
    </form>
   </div><!-- class=block //-->
<?php
}

print_footer($show_links=false);
?>  
