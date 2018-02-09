#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./sw_insert_deli_trace.php >& $DIR/log/sw_insert_deli_trace_`date +%Y%m%d`.log

popd > /dev/null
