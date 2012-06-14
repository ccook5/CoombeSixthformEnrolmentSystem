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
