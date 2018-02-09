#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_auto_order_cancel.php >> $DIR/log/cr_auto_order_cancel_`date +%Y%m%d`.log 2>&1

popd > /dev/null
