<?php
?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Shopping List Priorities</title>
    <link rel="stylesheet" href="vendor/kraksoft/jquery-ui/css/smoothness/jquery-ui.custom.min.css" />
    <script src="vendor/kraksoft/jquery/js/jquery.min.js"></script>
    <script src="vendor/kraksoft/jquery-ui/js/jquery-ui.custom.min.js"></script>
    <style>
    #sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
    #sortable li span { position: absolute; margin-left: -1.3em; }
    </style>
    <script>
    $(function() {
        $( "#sortable" ).sortable({
		stop: function( event, ui ) {
			console.log($.map($(this).find('li'), function(el) {
				return el.id + ' = ' + $(el).index();
			}));
		}
	});
        $( "#sortable" ).disableSelection();
	$.getJSON("list.php/items", function(result){
		for (var i in result)
		{
			if (result.hasOwnProperty(i))
			{
				$('#sortable').append('<li id="' + result[i]._id + '" class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' + result[i].name + '</li>');
			}
		}
		$( "#sortable" ).show('blind', {}, 700);
	});
    });
    </script>
</head>
<body>

<ul id="sortable" style="display: none;"></ul>

</body>
</html>
