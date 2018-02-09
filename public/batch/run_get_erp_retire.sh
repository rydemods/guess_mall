#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_get_erp_retire.php >& $DIR/log/cr_get_erp_retire_`date +%Y%m%d%H%M%S`.log

popd > /dev/null
