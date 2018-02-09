#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_get_erp_o2o_fees.php >& $DIR/log/cr_get_erp_o2o_fees_`date +%Y%m%d%H%M%S`.log

popd > /dev/null
