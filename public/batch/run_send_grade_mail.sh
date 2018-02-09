#!/bin/sh

DIR=`dirname $0`;

pushd $DIR > /dev/null

./cr_send_grade_mail.php >& $DIR/log/cr_send_grade_mail_`date +%Y%m%d%H%M%S`.log

popd > /dev/null
