#!/bin/bash
PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/bin
# JJP SITA 22-10-2017
recipients="pieterse.johan3@gmail.com,johan.pieterse@sita.co.za"

# Check MQ-PUT folder for transfer of files to Web

cd /var/www/html/atom
rm -f atom_publish_monitor.txt
#list all files in folder to atom_publish_monitor.txt
find /var/mqm/transfer/put/ > atom_publish_monitor.txt
# cd to the directory that contains the file we want to email 

# send the email with the unix/linux mail command
mail -s "/var/mqm/transfer/put directory content" "$recipients" < atom_publish_monitor.txt



