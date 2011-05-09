<?php
include("../includes/functions.php");
$full_states_arr = get_full_node_summary();

$total_unknown 		= $full_states_arr[0];
$total_down		= $full_states_arr[1];
$total_idle		= $full_states_arr[2];
$total_allocated	= $full_states_arr[3];
$total_error		= $full_states_arr[4];
$total_mixed		= $full_states_arr[5];
$total_future		= $full_states_arr[6];
$total_end		= $full_states_arr[7];

/* update the rrd tool file */
$fname = "/tmp/summary.rrd";

$ret = rrd_update($fname, "N:$total_unknown:$total_down:$total_idle:$total_allocated:$total_error:$total_mixed:$total_future:$total_end");

if( $ret == 0 )
{
	$err = rrd_error();
	echo "ERROR occurred: $err\n";
}

?>
