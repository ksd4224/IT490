#!/bin/bash

##Change the IP's here the rest of the script will adjust appropriately##
rbmq=nikitas@10.248.179.14
apc=ksd42@10.248.179.34
dbs=rp54@10.248.179.18
bck=adam@10.248.179.22

################################## RabbitMQ ################################################
#rabbitmq=$(ping -c 2 192.168.129.136)

#if [[ $rabbitmq == *"64 bytes from 192.168.129.136:"* ]]; then
#	echo "Checking Status"
#	check=$(systemctl --host $rbmq status rabbitmq-server.service)
#	if [[ $check == *"active (running)"* ]]; then
#		echo "Service is running"
#
#	else
#		#ssh-copy-id nikitas@10.248.179.14
#		echo "Starting services.."
#		ssh $rbmq sudo service rabbitmq-server start
#		check=$(systemctl --host $rbmq status rabbitmq-server.service)
#	        if [[ $check == *"active (running)"* ]]; then
#       	        echo "Service is running"
#			echo $check
#		fi
#	fi
#else
#	echo "server is down"
#
#fi

################################## Apache ################################################
#Apache=$(ping -c 2 192.168.129.136)
#
#if [[ $Apache == *"64 bytes from 192.168.129.136:"* ]]; then
#        echo "SSH into server"
#change user and IP        check=$(systemctl --host $apc status apache2)
#        if [[ $check == *"active (running)"* ]]; then
#                echo "Service is running"
#
#        else
#                echo "Starting services.."
#		 ssh-copy-id ksd42@10.248.179.14
#		 ssh $apc sudo service apache2 start
#                check=$(systemctl --host $apc status apache2)
#                if [[ $check == *"active (running)"* ]]; then
#                        echo "Service is running"
#                        echo $check
#                fi
#        fi
#else
#        echo "server is down"
#
#fi
#


################################## Database ################################################
#Database=$(ping -c 2 192.168.129.136)

#if [[ $database == *"64 bytes from 192.168.129.136:"* ]]; then
#        echo "SSH into server"
#        check=$(systemctl --host $dbs status mysql.service)
#        if [[ $check == *"active (running)"* ]]; then
#                echo "Service is running"

#        else
#                echo "Starting services.."
#change IP	 ssh-copy-id rp54@10.248.179.14
#                ssh $dbs sudo service mysql.service start
#                check=$(systemctl --host $dbs status mysql.service)
#                if [[ $check == *"active (running)"* ]]; then
#                        echo "Service is running"
#                        echo $check
#                fi
#        fi
#else
#        echo "server is down"

#fi


################################## Backend ################################################
#backend=$(ping -c 2 192.168.129.136)

#if [[ $backend == *"64 bytes from 192.168.129.136:"* ]]; then
#        echo "SSH into server"
#        check=$(systemctl --host $bck status rabbitmq-server.service)
#        if [[ $check == *"active (running)"* ]]; then
#               echo "Service is running"
#		echo launching listener
#		cd /

#        else
#                echo "Starting services.."
#Change IP	 ssh-copy-id adam@10.248.179.22
#                sudo systemctl --host $bck start rabbitmq-server.service
#                check=$(systemctl --host $bck status rabbitmq-server.service)
#                if [[ $check == *"active (running)"* ]]; then
#                        echo "Service is running"
#                        echo $check
#                fi
#        fi
#else
#        echo "server is down"

#fi

