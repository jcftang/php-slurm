<?php
/* create the rrd tool file */
$fname = "/tmp/summary.rrd";

$opts = array( "--step", "300", "--start", 0,
               "DS:unknown:GAUGE:600:U:U",
               "DS:down:GAUGE:600:U:U",
               "DS:idle:GAUGE:600:U:U",
               "DS:allocated:GAUGE:600:U:U",
               "DS:error:GAUGE:600:U:U",
               "DS:mixed:GAUGE:600:U:U",
               "DS:future:GAUGE:600:U:U",
               "DS:end:GAUGE:600:U:U",
	       "RRA:AVERAGE:0.5:1:600",
	       "RRA:AVERAGE:0.5:6:700",
	       "RRA:AVERAGE:0.5:24:775",
	       "RRA:AVERAGE:0.5:288:797",
	       "RRA:MAX:0.5:1:600",
	       "RRA:MAX:0.5:6:700",
	       "RRA:MAX:0.5:24:775",
	       "RRA:MAX:0.5:288:797"
	       );

$ret = rrd_create($fname, $opts, count($opts));

if( $ret == 0 ) {
    $err = rrd_error();
    echo "Create error: $err\n";
  }
?>
