$("span.big_number").mouseover(function(){
	if($(this).html() != "0") {
		var id = $(this).attr('id');
		var state_regexed="";
		var partname_regexed="";
		state_regexed = id.replace(/^[a-zA-Z]*\[/g,'');
		partname_regexed = id.replace(/\[\d*/,'');
		$.ajax({ url: 'includes/functions.php',
			data: {action: 'filter_nodes_partition_state',pname: partname_regexed, state: state_regexed},
			type: 'post',
			success: function(output) {
				if(output!=-1) {
					var node_arr = output.split("<|>");
					var selector;
					$.each(node_arr, function(index, value) {
						selector = "#"+value;
						var var_class = $(selector).attr('class');
						$(selector).removeClass(var_class);
						var_class = var_class+'_over';
						$(selector).addClass(var_class);
					});
				}
			}
		});
	}
}).mouseout(function(){
	if($(this).html() != "0") {
		var id = $(this).attr('id');
                var state_regexed="";
                var partname_regexed="";
                state_regexed = id.replace(/^[a-zA-Z]*\[/g,'');
                partname_regexed = id.replace(/\[\d*/,'');
                $.ajax({ url: 'includes/functions.php',
                        data: {action: 'filter_nodes_partition_state',pname: partname_regexed, state: state_regexed},
                        type: 'post',
                        success: function(output) {
                                if(output!=-1) {
                                        var node_arr = output.split("<|>");
                                        var selector;
                                        $.each(node_arr, function(index, value) {
                                                selector = "#"+value;
                                                var var_class = $(selector).attr('class');
						var lngth = var_class.length;
						var class_regexed = var_class.replace(/_over*/g,'');
                                                $(selector).removeClass(var_class);
                                                $(selector).addClass(class_regexed);
                                        });
                                }
                        }
                });
        }
});
