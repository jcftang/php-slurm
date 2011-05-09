$(document).ready(function() {
	$(".is_link").hover(function() {
		$(this).parent().removeClass("not_over_link");
		$(this).parent().addClass("over_link");
	},function(){
		$(this).parent().removeClass("over_link");
		$(this).parent().addClass("not_over_link");
	});
});

$(window).load(function() {
	resizeMain();
});

function loadPage(link) {
	window.location = link;
}

function resizeMain() {
	var w_main = $('#slurm_ping').width() + $('#link_container').width() + parseInt($('#slurm_ping').css("margin-left"))*3 + 5;
	$('#main').css('width',w_main);
	var t_functions = $('#main').height()
                        + parseInt($('#main').css('margin-top')) + 10;
        var cssObj = {
                'top'   : t_functions
        }
        $("#function_results").css(cssObj);
}
