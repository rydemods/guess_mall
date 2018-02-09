#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_auto_price_update.php >& $DIR/log/cr_auto_price_update_`date +%Y%m%d%H%M%S`.log

popd > /dev/null
