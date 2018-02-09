#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_send_birthday_mail.php >> $DIR/log/cr_send_birthday_`date +%Y%m%d`.log 2>&1

popd > /dev/null
