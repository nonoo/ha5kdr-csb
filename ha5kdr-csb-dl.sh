#!/bin/bash

self=`readlink "$0"`
if [ -z "$self" ]; then
	self=$0
fi
scriptname=`basename "$self"`
scriptdir=${self%$scriptname}

wget 'http://nmhh.hu/amator/call_sign_book.xml' -O /tmp/csb.xml &>/dev/null
if [ $? -eq 0 ]; then
	$scriptdir/ha5kdr-csb-process.php /tmp/csb.xml
else
	echo "Error while downloading csb!" | mail -s "[`hostname`] error downloading csb" nonoo@nonoo.hu
fi
rm -f /tmp/csb.xml
