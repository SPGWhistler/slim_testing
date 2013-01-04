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
    #sortable li span.ui-icon { position: absolute; margin-left: -1.3em; }
    #sortable li span.name {  }
    </style>
    <script>
    $(function() {
        $( "#sortable" )
		.sortable({
			stop: function( event, ui ) {
				console.log($.map($(this).find('li'), function(el) {
					return el.id + ' = ' + $(el).index();
				}));
			}
		})
        	.disableSelection();
	$('#sortable').delegate('li', 'click', function(){
		var $label = $(this).find('span.name');
		var old_value = $label.text();
		$label.replaceWith('<span class="edit"><input type="text" value="' + old_value + '" /></span>');
		var $edit = $(this).find('span.edit>input');
		$edit.select();
		$edit.blur(function(){
			console.log($edit.val());
		});
	});
	$("#remove_all")
		.click(function(){
			$( "#dialog-confirm" ).dialog({
				resizable: false,
				height:240,
				width:500,
				modal: true,
				buttons: {
					"Delete all items": function() {
						$( this ).dialog( "close" );
						$.post("list.php/remove/items", function(){
							loadItems();
						});
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
		})
		.button();
	$("#new_item")
		.click(function(){
			$.ajax({
				type: 'PUT',
				url: 'list.php/item/untitled',
				success: function(){
					loadItems();
				}
			});
		})
		.button();
	function loadItems()
	{
		$('#sortable').empty();
		$.getJSON("list.php/items", function(result){
			for (var i in result)
			{
				if (result.hasOwnProperty(i))
				{
					$('#sortable').append('<li id="' + result[i]._id + '" class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><span class="name">' + result[i].name + '</span></li>');
				}
			}
			$( "#sortable" ).show('blind', {}, 700);
		});
	}
	loadItems();
    });
    </script>
</head>
<body>

<ul id="sortable" style="display: none;"></ul>
<button id="remove_all">Remove All Items</button>
<button id="new_item">New</button>

<div id="dialog-confirm" style="display: none;" title="Remove all items from the list?">
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>All items will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

</body>
</html>
