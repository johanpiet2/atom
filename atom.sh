#!/bin/bash
PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/bin
# JJP SITA 05-12-2014
# Added options to move the imported file to another location -r
# Import AtoM XML

#.pid file name
pidFileName="import.pid"

#paths
appPath="/var/www/html/atom"
mqPath="/var/mqm/transfer/get"

#.pid file name and path
PIDFILE="$appPath/$pidFileName"

# If PID file exists, it can be read, the contents aren't blank, and the process still exists,
# it's already running.
if [ -f $PIDFILE ] && read PID < $PIDFILE && [ ! -z "$PID" ] && [ -d /proc/$PID ]
then
	#echo `date +%Y-%m-%d:%H:%M:%S`": p Id:" $PID
    #echo `date +%Y-%m-%d:%H:%M:%S`": Already started" >&2
    exit 0
fi

# if not create the file:
echo $$ > "${PIDFILE}"
chmod 777 "${PIDFILE}"

#first move all files to import folder
cd $mqPath
mv $mqPath/*.xml $appPath/uploads/upload

#go to import/upload folder and import files
cd $appPath
php -d memory_limit=4096M symfony import:bulk -v -r"$appPath/uploads/imported" --output="$appPath/NAAIRS_Imported_Data.txt" "$appPath/uploads/upload"
# remove the file when the script finishes:

echo `date +%Y-%m-%d:%H:%M:%S`": Remove .pid"
rm PIDFILE
echo `date +%Y-%m-%d:%H:%M:%S`": Remove process"
kill $PID
cd pwd
rm "$appPath/import.pid"
cd pwd
rm "$appPath/import.pid"
cd pwd
echo `date +%Y-%m-%d:%H:%M:%S`": Remove .pid completed"
echo `date +%Y-%m-%d:%H:%M:%S` $PID
ls "$appPath/import.pid"


