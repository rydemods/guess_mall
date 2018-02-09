#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_get_erp_shopinfo.php >& $DIR/log/cr_get_erp_shopinfo_`date +%Y%m%d%H%M%S`.log

popd > /dev/null
