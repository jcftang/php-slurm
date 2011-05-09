//	Calculate the width to resize our hostlist container by
//	we use the table inside of it to set its width
$('#node_hostlist_container').width($('#sec_func_inner').width()+16);
$('#node_summary_container').width($('#sec_func_inner2').width()+16);
var var_l = parseInt($('#node_hostlist_container').width()) + parseInt($('#node_hostlist_container').css('margin-left')) + 16;
var cssObj = {
	'position' : 'absolute',
	'left' : var_l,
	'top' : '0px'
}
$('#node_summary_container').css(cssObj);
var var_t = parseInt($('#node_summary_container').css('top')) + parseInt($('#node_summary_container').height()) + 13;
var cssObj2 = {
	'position' : 'absolute',
	'top' : var_t
}
$('#legend_grid').css(cssObj2);
