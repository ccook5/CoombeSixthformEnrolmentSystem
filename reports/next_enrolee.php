<?php 

require_once('../config.inc.php');
require_once('../header.inc.php');

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = true, $script = "
  WebFontConfig = {
    google: { families: [ 'Play:400,700:latin', 'Aldrich::latin' ] }
  };
  (function() {
    var wf = document.createElement('script');
    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
      '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
  })();
$(document).ready(function() {

	document.body.setAttribute('style', 'background-color: white');
	setInterval (function() {
		var request = $.ajax({
			url: '/api/get_next_enrolee.php',
			type: 'POST',
			data: {action              : 'a'},
			dataType: 'html'
		});

		request.done(function(msg) {
		  $('#debug').html( msg );
		});
	}, 500);
});

",
			$exclude_datatables_js = true
//			$meta                  ="      <meta http-equiv='refresh' content='1;url=/reports/next_enrolee.php'/>"

);
?>

<!--<h1 style="font-size: 500px; border: 0px solid black; margin: 0px; text-align: center;"><?php echo($config['next_enrolee']); ?>-->

<div style="color: #0FA550; font-family: Aldrich; font-size: 500px; border: 0px solid black; margin: 0px; text-align: center;" id='debug'></div>

</body>
</html>