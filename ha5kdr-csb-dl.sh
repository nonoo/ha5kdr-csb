#!/bin/bash

self=`readlink "$0"`
if [ -z "$self" ]; then
	self=$0
fi
scriptname=`basename "$self"`
scriptdir=${self%$scriptname}

wget 'http://nmhh.hu/amator/call_sign_book.xml' -O /tmp/csb.xml &>/dev/null
iconv -f ISO-8859-2 -t UTF-8 /tmp/csb.xml > /tmp/csb_tmp.xml
mv /tmp/csb_tmp.xml /tmp/csb.xml
sed -i'' -e 's/ISO-8859-1/UTF-8/g' /tmp/csb.xml
$scriptdir/ha5kdr-csb-process.php /tmp/csb.xml
rm -f /tmp/csb.xml
