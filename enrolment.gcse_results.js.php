<?php header('Content-type: text/javascript'); 
require_once 'config.inc.php';
require_once 'select_widget.php';
?>

/** Updates the grade selectbox depending on the gcse type. 
  *
  * Because you can't hide an option within a select on all browsers. JS 
  * designers are idiots. 
  *
  * See earlier in the php code because once you do this, selectedIndex refers only to the 
  * elements that arn't wraped in span tags. However, when you look up the 
  * option via dom using the new selectedIndex, you get the wrong option...
  *
  * Now fixed with some jquery...
  *
  */
function update_grade_selectbox()
{
	$('option.gcse_grade').each( function(index) {
		if (this.value != $('select.gcse_type option:selected')[0].id) { 
			if (this.parentElement.nodeName != 'SPAN') {
				$(this).wrap('<span style=\"none\" />');
			}
		} else { 
			if (this.parentElement.nodeName == 'SPAN') {
				$(this).unwrap();
			}
		}
	});
};

/** Code specific to the students_results.php page. */
function students_results(ResultsTable)
{

/** This runs when the user clicks the save button in a row. */
	function restoreRow ( oTable, nRow )
	{
		var aData = oTable.fnGetData(nRow);
		var jqTds = $('>td', nRow);
		for ( var i=0, iLen=jqTds.length ; i<iLen ; i++ ) {
			oTable.fnUpdate( aData[i], nRow, i, false );
		}
		oTable.fnUpdate( '<button class=\"edit\">Edit</button>', nRow, 5, false );
		oTable.fnDraw();
	}
	
<?php
// This block creates the javascript code that creates the selectbox elements.
	echo(create_select_builder('build_GCSE_Type_select',     "SELECT * from GCSE_Qualification ORDER BY id",      'gcse_type',    'id', 'Type', 'Length'));
	echo(create_select_builder('build_subject_names_select', "SELECT * from GCSE_Subjects",                       'subject_name', 'id', 'Name'));
	echo(create_select_builder('build_GCSE_Grade_selects',   "SELECT * FROM GCSE_Grade ORDER BY QualificationID", 'gcse_grade',   'id', 'Grade', ''/*'Points'*/, 'QualificationID'));
?>

/** This is run when a user clicks Edit or Save. 
  *
  * We take the value from some cells and create some select boxes with those entries pre-selected.
  *
  * We trigger the above function to update the grade select box depending on the pre-selected value from GCSE_Type.
  *
  * Finally, we change the Edit button to a Save button. When clicked, the callback functions action depends on the value of this.
  */
	function editRow ( oTable, nRow )
	{
		var aData = oTable.fnGetData(nRow);
		var jqTds = $('>td', nRow);
		
		//skip column 0 and 1, they are currently id fields that we need, but 
		jqTds[2].innerHTML = build_GCSE_Type_select    (aData[2]);
		jqTds[3].innerHTML = build_subject_names_select(aData[3]);
		jqTds[4].innerHTML = build_GCSE_Grade_selects  (aData[4]);
		
// TODO: What is this bit for? It doesn't seem to trigger alert()...
		$('select.gcse_grade > options').each( function(index) {
			if (index == $('select.gcse_grade > options')[$('select',nRow)[2].selectedIndex])
			{
				alert(index);
			}
		} );

		update_grade_selectbox();
		oTable.fnUpdate( '<button class=\"edit\">Save</button>', nRow, 5, false );
	}
	
/** Post the data from this row to ajax_update_students_results.php via AJAX.
  */
	function saveRow ( oTable, nRow, action )
	{
		var jqSelects = $('select', nRow);
		var jqTds = $('>td', nRow);
		var xmlhttp;
		
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
		}
		xmlhttp.open('POST', 'ajax_update_students_results.php', false);
		xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		
		http_data  = 'StudentID='  + jqTds[1].innerHTML;
		http_data += '&ResultID='  + jqTds[0].innerHTML;
		http_data += '&SubjectID=' + jqSelects[1].options[jqSelects[1].selectedIndex].id;
		http_data += '&GradeID='   + $('select.gcse_grade > option')[jqSelects[2].selectedIndex].id;

		if (action == 'Save') {
			http_data  = 'action=update&' + http_data;
			xmlhttp.send(http_data);
		} else {
			http_data  = 'action=new&' + http_data;
			xmlhttp.send(http_data);
		}
		document.getElementById('debug').innerHTML = xmlhttp.responseText;

		oTable.fnUpdate( jqSelects[0].options[jqSelects[0].selectedIndex].text, nRow, 2, false );
		oTable.fnUpdate( jqSelects[1].options[jqSelects[1].selectedIndex].text, nRow, 3, false );
		oTable.fnUpdate( $('select.gcse_grade > option')[jqSelects[2].selectedIndex].text, nRow, 4, false );
		
		oTable.fnUpdate( '<button class=\"edit\">Edit</button>', nRow, 5, false );
		oTable.fnDraw();
	}

	var nEditing = null;

/** Add a click handler to the rows. This adds a highlight to the currently selected row. 
  * 
  * This could also be used as a callback to do something with the row.
  */
	$('#results tbody').click( function( event )
	{
		$(ResultsTable.fnSettings().aoData).each( function ()
		{
			$(this.nTr).removeClass('row_selected');
		});
		$(event.target.parentNode).addClass('row_selected');
	}) ;
	
/** A click handler for the "New Result" link. This is similar to the edit click handler.
  * The main difference is that it adds a new row. 
  */
	$('#new_result').click( function (e)
	{
		e.preventDefault();
		
		var aiNew = "";
		
<?php if ($config["debug"] == "true") { ?>
		aiNew = ResultsTable.fnAddData( ['', StudentID, '', '', '',
			'<button class=\"edit\" id=\"test\">Add</button>',
			'<button class=\"delete\">Delete</button>' ] );
<?php } else { ?>
		aiNew = ResultsTable.fnAddData( [ '', '', '',
			'<button class=\"edit\" id=\"test\">Add</button>',
			'<button class=\"delete\">Delete</button>' ] );
<?php } ?>

		var nRow = ResultsTable.fnGetNodes( aiNew[0] );
		
		if ( nEditing !== null && nEditing != nRow ) {
			/* Currently editing - but not this row - restore the old before continuing to edit mode */
			restoreRow( ResultsTable, nEditing );
		}
		editRow( ResultsTable, nRow );
		nEditing = nRow;
		update_grade_selectbox();
		
		ResultsTable.fnUpdate( '<button class=\"edit\">Add</button>', nRow, 5, false );
	} );

/** Delete Click handler. Calls 'ajax_update_students_results.php via AJAX, then deletes the row in the datatable.
  */
	$('.delete').live('click', function (e)
	{
		e.preventDefault();
		
		var nRow     = $(this).parents('tr')[0];
		var jqTds    = $('>td', nRow);
		
		var xmlhttp;
		
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
		}

		xmlhttp.open('POST', 'ajax_update_students_results.php', false);
		xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlhttp.send('action=delete&ResultID='+jqTds[0].innerHTML);
	
		document.getElementById('debug').innerHTML = xmlhttp.responseText;
		
		ResultsTable.fnDeleteRow( nRow );
	} );
	
	
	$(".edit").button();
	$(".delete").button();
	
	$('.edit').live('click', function (e)
	{
		/* Get the row as a parent of the link that was clicked on */
		var nRow = $(this).parents('tr')[0];
		
		if ( nEditing !== null && nEditing != nRow ) {
			/* Currently editing - but not this row - restore the old before continuing to edit mode */
			restoreRow( ResultsTable, nEditing );
			editRow( ResultsTable, nRow );
			nEditing = nRow;
		}
		else if ( nEditing == nRow && this.innerHTML == 'Save') {
			/* Editing this row and want to save it */
			saveRow( ResultsTable, nEditing, 'Save' );
			nEditing = null;
		}
		else if ( nEditing == nRow && this.innerHTML == 'Add') {
			/* Editing this row and want to save it */
			saveRow( ResultsTable, nEditing, 'Add' );
			nEditing = null;
		}
		else {
			/* No edit in progress - lets start one */
			editRow( ResultsTable, nRow );
			nEditing = nRow;
		}
	} );

	$('select.gcse_type').live('change', update_grade_selectbox );
}