<?php

require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');

// get a list of student/course types

$sql          = "SELECT * FROM StudentTypes;";
$result       = mysql_query($sql, $link);
$course_types = Array();
$first_course = "";

if (!$result)
{
  die('Invalid query: ' . mysql_error());
}
else
{
  while($row = mysql_fetch_array($result))
  {
	if ($first_course == "") {
		$first_course = $row['id'];
	}
	$course_types[$row['id']] = $row['CourseType'];
  }
}

if (! isset($_GET['block_id']))
{
    print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = true, $script = "", $exclude_datatables_js = false, $meta="<meta http-equiv='refresh' content='0;url=blocks.php?block_id=".$first_course."'>");
	die();
}
else if ($_GET['block_id'] == "null")
{
	die;
}
else
{
	$block_id = mysql_real_escape_string($_GET['block_id']);
}

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = true, $script = "
	$(function() {
		$( '#slider' ).slider(
		{
			min: 1,
			max: 100,
			range: 'min',
			value: 30,
			slide: function( event, ui ) {
				$('#MaxPupils')[0].value = ui.value - 1;
			}
		});
	} );
	function update_slider(event) {
		$( '#slider' ).slider( 'value', event.target.value );
	}
	function add_to_block(event, block_id)
	{
		var block   = event.target.parentNode.firstChild;
		var cID     = $( 'select#course' )[0].selectedOptions[0].value;
		var cName   = $( 'select#course' )[0].selectedOptions[0].innerHTML;
		var MaxPpls = $('#MaxPupils')[0].value;
		
//			alert('add to block,'+block_id+', '+cName+cName+MaxPupils);
		
		var request = $.ajax({
			url: 'ajax_update_block.php',
			type: 'POST',
			data: { 
				action             : 'new',
				BlockID            : block_id,
				MaxPupils          : MaxPpls,
				CourseDefID        : cID
			},
			dataType: 'html'
		} );

		request.done(function( msg ) {
			$('#debug').html( msg );
//				alert('success:'+msg);

			var html = '<tr><td style=\'height: 2.1em\'>';
			html += cName + ' ['+MaxPpls+']';
			html += '<input type=\'button\' style=\'float: right;\' onClick=\'remove_course2(event, '+block_id+', \"'+cName+'\", '+cID+', '+MaxPpls+')\' value=\'X\' /></td></tr>';
			var elem = $(html);
			elem.appendTo(block);
		} );

		request.fail(function(jqXHR, textStatus) {
			alert( 'Request failed: ' + textStatus );
		} )
	}
	
	function remove_course(event, block_id, course_name, course_id)
	{
//			alert('remove'+block_id+', '+course_name+', '+course_id);
		
		var request = $.ajax({
			url: 'ajax_update_block.php',
			type: 'POST',
			data: { 
				action             : 'delete',
				id                 : course_id
			},
			dataType: 'html'
		} );

		request.done(function( msg ) {
			$('#debug').html( msg );
			alert('success1:'+msg);
			$('#course_'+course_id).remove();
		} );

		request.fail(function(jqXHR, textStatus) {
			alert( 'Request failed: ' + textStatus );
		} )
	}
	
	function remove_course2(event, block_id, course_name, course_id, MaxPpls)
	{
//			alert('remove'+block_id+', '+course_name+', '+course_id);
		
		var request = $.ajax({
			url: 'ajax_update_block.php',
			type: 'POST',
			data: { 
				action             : 'delete',
				BlockID            : block_id,
				MaxPupils          : MaxPpls,
				CourseDefID        : course_id
			},
			dataType: 'html'
		} );

		request.done(function( msg ) {
			$('#debug').html( msg );
			alert('success2:'+msg);
			$('#course_'+course_id).remove();
		} );

		request.fail(function(jqXHR, textStatus) {
			alert( 'Request failed: ' + textStatus );
		} )
	}
	
	function edit_course(event, block_id, course_name, course_id, MaxPpls)
	{
//		alert('test - edit');
		var t = event.target.parentNode.parentNode.parentNode;
		
		$('#ip_'+course_id)[0].disabled=false;
		var html = \"<span style='border: 1px solid black;' id='current_button' class='ui-icon ui-icon-check' onClick='save_course(event, \"+block_id+\", \"+course_id+\")'> </span>\";
		    html += \"<span style='border: 1px solid black;' id='current_button' class='ui-icon ui-icon-close' onClick='cancel_save_course(event, \"+course_id+\", \"+$('#ip_'+course_id)[0].value+\")'> </span>\";
		 
		$('#ip_'+course_id).after(html);
	}
	
	function save_course(event, block_id, cID)
	{
		$('#ip_'+cID)[0].disabled=true;		
		var request = $.ajax({
			url: 'ajax_update_block.php',
			type: 'POST',
			data: { 
				action             : 'update',
				id                 : cID,
				BlockID            : block_id,
				MaxPupils          : $('#ip_'+cID)[0].value
			},
			dataType: 'html'
		} );

		request.done(function( msg ) {
			$('#debug').html( msg );
//			alert('success:'+msg);
		} );

		request.fail(function(jqXHR, textStatus) {
			alert( 'Request failed: ' + textStatus );
		} )
	// this is done twice deliberatly...
		$('#current_button').remove();
		$('#current_button').remove();
	}
	
	function cancel_save_course(event, course_id, MaxPpls)
	{
		$('#ip_'+course_id)[0].disabled=true;
		$('#ip_'+course_id)[0].value=MaxPpls;
	// this is done twice deliberatly...
		$('#current_button').remove();
		$('#current_button').remove();
	}
", $exclude_datatables_js = true, $meta = "",
			$extra_script="");


foreach($course_types as $ct_key => $ct_value)
{
	echo('     <a href="blocks.php?block_id='.($ct_key)."\">\n");
	echo('      '.$ct_value."</a> |\n");
}

/** Print the html table for the students enrolments.
  *
  * We use get_students_current_enrolments() to get a list of what they have allready.
  * This table contains form elements that should let us submit changes to the enrolments.
  * This form should post the data to blocks_handler.php
  */

echo("      <div class='block'><table class='with-borders-horizontal'>\n");
echo("       <tr>\n");

$blocks             = Array();
$i                  = 0;
$cols = 0;

// Get a list of the current blocks from the database. This should be a list like 'a','b','c',etc.
$sql_blocks    = "SELECT * FROM BLOCKS_Blocks WHERE Year=".$config['current_year']." AND CourseType='".$block_id."' ORDER BY id";
$result_blocks = mysql_query($sql_blocks, $link);

if ($result_blocks)
{
	$cols = mysql_num_rows($result_blocks);
	while($row_blocks = mysql_fetch_array($result_blocks))
	{
		echo ("<td style='padding: 0px; margin: 0px; padding-bottom: 30px; position: relative;'><table>");
		echo ("      <thead><th width='10%'>".$row_blocks['Name']."</th></thead>");
		
		/** Get all the current courses for the current block.
		  *
		  * We build a list for each block, then print by row in a loop 
		  * later on in the code, so this isn't inefficient. 
		  */
		$sql    = "SELECT * FROM BLOCKS_Course INNER JOIN BLOCKS_CourseDef ON CourseDefID=BLOCKS_CourseDef.id";
		$sql   .= " INNER JOIN StudentTypes ON Type=StudentTypes.id";
		$sql   .= " WHERE EnrolmentYear=".$config['current_year']." AND BlockID=".$row_blocks['id'];
		$result = mysql_query($sql, $link);

		if ($result) {
			$i = 0;
			while($row = mysql_fetch_array($result))
			{
?>
      <tr>
	   <td style='height: 2.1em' id='course_<?php echo($row[0]); ?>'>
        <?php echo($row['SubjectName']." <br /><input type='text' id='ip_".$row[0]."' value='".$row["MaxPupils"]."' disabled='disabled' size='2' />"); ?>
		<div style='float: right;'>
		<span
		 style='border: 1px solid black;'
		 class='ui-icon ui-icon-trash'
		 onClick='remove_course(event, <?php echo($row_blocks['id'].", \"".$row["SubjectName"]."\", ".$row[0]); ?>)'> </span>
		<span
		 style='border: 1px solid black;'
		 class='ui-icon ui-icon-pencil'
		 onClick='edit_course(event, <?php echo($row_blocks['id'].", \"".$row["SubjectName"]."\", ".$row[0]); ?>)'> </span>
		</div>
	   </td>
	  </tr>
<?php	
				$i += 1;
			}
			echo("</table>");
			
			echo "<input type='button' onClick=\"add_to_block(event, ".$row_blocks['id'].")\" value='Add' style='position: absolute; bottom: 2px; left: 2px;' />";
		} else {  die('Invalid query: ' . mysql_error());  }
	}
} else {  die('Invalid query: ' . mysql_error());  }
	
echo("       </tr>\n");
echo("       <tr><td colspan='".$cols."'>\n");

// Get a list of the current course definitions
$sql    = "SELECT * FROM BLOCKS_CourseDef WHERE Type='".$block_id."' ORDER BY id";
$result = mysql_query($sql, $link);

if ($result)
{
	print('<select id="course">');
	while($row = mysql_fetch_array($result))
	{
		print('<option value="'.$row['id'].'">'.$row['SubjectName'].'</option>');
	}
	print('</select>');
}
?><br />
		 <label for="MaxPupils">Maximum Pupils for course</label>
         <input type="textbox" value="30" name="MaxPupils" id="MaxPupils" onkeyup="update_slider(event);" />
         <div id="slider"></div>
        </td>
	   </tr>
      </table></div>
