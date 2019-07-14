#!/bin/bash
PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/bin
# JJP SITA 05-12-2014
# Added options to move the imported file to another location -r
# Move exported AtoM XML/CSV/Image

#.pid file name
pidFileName="import.pid"

#paths
appPath="/var/www/html/atom"
mqPath="/var/mqm/transfer/put"

#.pid file name and path
PIDFILE="$appPath/$pidFileName"

# If PID file exists, it can be read, the contents aren't blank, and the process still exists,
# it's already running.
if [ -f $PIDFILE ] && read PID < $PIDFILE && [ ! -z "$PID" ] && [ -d /proc/$PID ]
then
    #echo "Already started" >&2
    exit 0
fi

#move all files to MQ folder
cd $appPath/uploads/mq
chown -R mqm:mqm *
#mv $appPath/uploads/mq/*.jpg $mqPath/images
mv $appPath/uploads/mq/*.jpg $mqPath/attachments
mv $appPath/uploads/mq/*.pdf $mqPath/attachments
mv $appPath/uploads/mq/*.mp3 $mqPath/attachments
mv $appPath/uploads/mq/* $mqPath/attachments

cd $appPath/uploads/publish
chown -R mqm:mqm *
#mv $appPath/uploads/publish/*.csv $mqPath/publish
mv $appPath/uploads/publish/*.csv $mqPath

cd $appPath/uploads/unpublish
chown -R mqm:mqm *
#mv $appPath/uploads/unpublish/*.csv $mqPath/unpublish
mv $appPath/uploads/unpublish/*.csv $mqPath
mv $appPath/uploads/unpublish/*.xml $mqPath

cd $appPath/uploads/updates
chown -R mqm:mqm *
#mv $appPath/uploads/updates/*.csv $mqPath/updates
mv $appPath/uploads/updates/*.csv $mqPath


