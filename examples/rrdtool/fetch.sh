#!/bin/sh
END=`date +%s`
START=`echo $END-3600|bc` # over the hour
rrdtool fetch /tmp/summary.rrd AVERAGE --start $START --end $END
