<?php
$fname = "/tmp/summary.rrd";

$opts = array( "--start", "-1d", "--vertical-label=Nodes",
	       "DEF:unknown=/tmp/summary.rrd:unknown:AVERAGE",
	       "DEF:down=/tmp/summary.rrd:down:AVERAGE",
	       "DEF:idle=/tmp/summary.rrd:idle:AVERAGE",
	       "DEF:allocated=/tmp/summary.rrd:allocated:AVERAGE",
	       "DEF:error=/tmp/summary.rrd:error:AVERAGE",
	       "DEF:mixed=/tmp/summary.rrd:mixed:AVERAGE",
	       "DEF:future=/tmp/summary.rrd:future:AVERAGE",
	       "DEF:end=/tmp/summary.rrd:end:AVERAGE",
	       "AREA:unknown#00335B:Unknown:STACK",
	       "AREA:down#14213D:Down:STACK",
	       "AREA:idle#00FF00:Idle:STACK",
	       "AREA:allocated#60AFDD:Allocated\\r:STACK",
	       "AREA:error#FF0000:Error:STACK",
	       "AREA:mixed#112151:Mixed:STACK",
	       "AREA:future#14213D:Future:STACK",
	       "AREA:end#0C1C47:End\\r:STACK"
               );

$ret = rrd_graph("/tmp/summary.gif", $opts, count($opts));

if( !is_array($ret) )
  {
    $err = rrd_error();
    echo "rrd_graph() ERROR: $err\n";
  }

?>
