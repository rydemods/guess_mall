#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_get_erp_product_update_v1.php >& $DIR/log/cr_get_erp_product_update_v1_`date +%Y%m%d%H%M%S`.log

popd > /dev/null
