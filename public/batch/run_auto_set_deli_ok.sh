#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_auto_set_deli_ok.php >& $DIR/log/cr_auto_deli_update_`date +%Y%m%d%H%M%S`.log

popd > /dev/null
