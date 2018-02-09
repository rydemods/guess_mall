#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_get_erp_wms_status.php >> $DIR/log/cr_get_erp_wms_status_`date +%Y%m%d`.log 2>&1

popd > /dev/null
