#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_send_alimtalk.php >& $DIR/log/cr_send_alimtalk_`date +%Y%m%d%H%M%S`.log

popd > /dev/null
