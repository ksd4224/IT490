#!/bin/bash
echo "Opening database"
systemctl start mysql.service
mysql --user=root --password=patel54
