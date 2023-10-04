#!/bin/bash

servstat=$(service mysql status)

if [[ $servstat == *"active (running)"* ]]; then
	echo "MySQL is already running."
else
	echo "Starting MySQL"
	systemctl start mysql.service
fi
