<?php
header('Content-type: text/javascript'); 
require_once('../config.inc.php');
?>

$(document).ready(function() {

/** This runs when the user clicks the save button in a row. */
	function restoreRow ( oTable, nRow )
	{
		var aData = oTable.fnGetData(nRow);
		var jqTds = $('>td', nRow);
		for ( var i=0, iLen=jqTds.length ; i<iLen ; i++ ) {
			oTable.fnUpdate( aData[i], nRow, i, false );
		}
		oTable.fnUpdate( '<button class=\"edit\">Edit Setting</button>', nRow, 3, false );
		oTable.fnDraw();
	}

/** This is run when a user clicks Edit or Save. 
  *
  * We take the value from some cells and create some select boxes with
  * those entries pre-selected.
  *
  * We trigger the above function to update the grade select box depending 
  * on the pre-selected value from GCSE_Type.
  *
  * Finally, we change the Edit button to a Save button. When clicked, the 
  * callback functions action depends on the value of this.
  */
	function editRow ( oTable, nRow, add )
	{
		var aData = oTable.fnGetData(nRow);
		var jqTds = $('>td', nRow);
		jqTds[0].innerHTML = '<input type="text" value="'+aData[0]+'">';
		jqTds[1].innerHTML = '<input type="text" value="'+aData[1]+'">';
		jqTds[2].innerHTML = '<input type="text" value="'+aData[2]+'">';

		if (add == true) {
			jqTds[3].innerHTML = '<button class=\"edit\">Add Setting</button>';
		}
		else
		{
			jqTds[3].innerHTML = '<button class=\"edit\">Save Setting</button>';
		}
	}
		
/** Post the data from this row to ajax_update_students_results.php via AJAX.
  */
	function saveRow ( oTable, nRow, action )
	{
		var jqInputs  = $('input',  nRow);
		var jqSelects = $('select', nRow);
		var act       = "new";

		if (action == 'Save') {
			act = 'update';
		}

		var request = $.ajax({
			url: 'ajax_update_config.php',
			type: 'POST',
			data: { 
				action             : act,
				setting            : jqInputs[0].value,
				value              : jqInputs[1].value,
				about              : jqInputs[2].value
			},
			dataType: 'html'
		} );

		request.done(function( msg ) {
			$('#debug').html( msg );
		} );

		request.fail(function(jqXHR, textStatus) {
			alert( 'Request failed: ' + textStatus );
		} )

		oTable.fnUpdate( jqInputs[0].value, nRow, 0, false );
		oTable.fnUpdate( jqInputs[1].value, nRow, 1, false );
		oTable.fnUpdate( jqInputs[2].value, nRow, 2, false );
		
		oTable.fnUpdate( '<button class=\"edit\">Edit Setting</button>', nRow, 3, false );
		oTable.fnDraw();
	}

	var nEditing = null;
	
	var settingsTable = $('#settings').dataTable( {
		'bProcessing': true,
		'sAjaxSource': 'get_settings.php',
		'sScrollY'   : '120px',
		'bPaginate'  : false,
		'fnRowCallback': function( nRow, aData, iDisplayIndex ) {
			$('td:eq(3)', nRow).html( '<button class=\"edit\">Edit Setting</button>' );
			$('td:eq(4)', nRow).html( '<button class=\"delete\">Delete</button>' );
		}
		//'aoColumnDefs': [ {
		///	'sClass'  : 'center',
		//	'aTargets': [ -1, -2 ]
		//} ]
	} );
	
//		makes buttons into jquery buttons
	$(".edit").button();
	$(".delete").button();

/** Add a click handler to the rows. This adds a highlight to the currently selected row. 
  */
	$('#settings tbody').click( function( event ) {
		$(settingsTable.fnSettings().aoData).each(function (){
			$(this.nTr).removeClass('row_selected');
		});
		$(event.target.parentNode).addClass('row_selected');
	} ) ;
	
	var nEditing = null;

	$('#settings .edit_results').live('click', function (event) {
		event.preventDefault();
	} );
	
/** Delete Click handler. Calls 'ajax_update_students_results.php 
  * via AJAX, then deletes the row in the datatable.
  */
	$('.delete').live('click', function (e)
	{
		e.preventDefault();

		var nRow     = $(this).parents('tr')[0];
		var jqTds    = $('>td', nRow);

		var request = $.ajax({
			url: 'ajax_update_config.php',
			type: 'POST',
			data: { 
				action             : 'delete',
				setting            : jqTds[0].innerHTML
			},
			dataType: 'html'
		} );

		request.done(function( msg ) {
			$('#debug').html( msg );
		} );

		request.fail(function(jqXHR, textStatus) {
			alert( 'Request failed: ' + textStatus );
		} )

		settingsTable.fnDeleteRow( nRow );
	} );
	
	$('#new_setting').click( function (e) {
		e.preventDefault();
		
		var aiNew = settingsTable.fnAddData( [ '', '', '',
			'<button class=\"edit\">Add Setting</button>', '<button class=\"delete\">Delete</button>' ] );
		var nRow = settingsTable.fnGetNodes( aiNew[0] );
		editRow( settingsTable, nRow, true );
		nEditing = nRow;
	} );
		
	$('.edit').live('click', function (e)
	{
		/* Get the row as a parent of the link that was clicked on */
		var nRow = $(this).parents('tr')[0];
		
		if ( nEditing !== null && nEditing != nRow ) {
			/* Currently editing - but not this row - restore 
			 * the old before continuing to edit mode          */
			restoreRow( settingsTable, nEditing );
			editRow( settingsTable, nRow );
			nEditing = nRow;
		}
		else if ( nEditing == nRow && this.innerHTML == 'Save Setting') {
			/* Editing this row and want to save it */
			saveRow( settingsTable, nEditing, 'Save' );
			nEditing = null;
		}
		else if ( nEditing == nRow && this.innerHTML == 'Add Setting') {
			/* Editing this row and want to save it */
			saveRow( settingsTable, nEditing, 'Add' );
			nEditing = null;
		}
		else {
			/* No edit in progress - lets start one */
			editRow( settingsTable, nRow );
			nEditing = nRow;
		}
	} );
} );
