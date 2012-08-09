<?php
header('Content-type: text/javascript'); 
require_once('../config.inc.php');
require_once('../select_widget.php');

?>
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
		var courseSelect = $( 'select#course' )[0];
		var cID     = courseSelect[courseSelect.selectedIndex].value;
		var cName   = courseSelect[courseSelect.selectedIndex].innerHTML;
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

			var html = '<tr><td style="height: 2.1em">';
			html += cName + ' ['+MaxPpls+']';
			html += '<input type="button" style="float: right;" onClick="remove_course2(event, "'+block_id+'", "'+cName+'", '+cID+', '+MaxPpls+')" value="X" /></td></tr>';
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
//			alert('success1:'+msg);
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
//			alert('success2:'+msg);
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
		var html = "<span style='border: 1px solid black;' id='current_button' class='ui-icon ui-icon-check' onClick='save_course(event, "+block_id+", "+course_id+")'> </span>";
		    html += "<span style='border: 1px solid black;' id='current_button' class='ui-icon ui-icon-close' onClick='cancel_save_course(event, "+course_id+", "+$('#ip_'+course_id)[0].value+")'> </span>";
		 
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