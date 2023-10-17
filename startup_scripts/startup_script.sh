#!/bin/bash

##Change the IP's here the rest of the script will adjust appropriately##
rbmq=nikitas@10.248.179.14
rbmq_IP=10.248.179.14
apc=ksd42@10.248.179.18
apc_IP=10.248.179.18
dbs=rp54@10.248.179.10
dbs_IP=10.248.179.10
bck=adam@10.248.179.22
bck_IP=10.248.179.22
################################## RabbitMQ ################################################
rabbitmq=$(ping -c 2 $rbmq_IP)

if [[ $rabbitmq == *"64 bytes from 10.248.179.14:"* ]]; then
	echo "Checking Status"
	check=$(systemctl --host $rbmq status rabbitmq-server.service)
	if [[ $check == *"active (running)"* ]]; then
		echo "Service is running"

	else
#		ssh-copy-id nikitas@10.248.179.14
		echo "SSH into rabbitmq"
		ssh $rbmq sudo service rabbitmq-server start
		check=$(systemctl --host $rbmq status rabbitmq-server.service)
	        if [[ $check == *"active (running)"* ]]; then
       	        	echo "Service is running"
			echo $check
		fi
	fi
else
	echo "server is down"

fi

################################## Apache ################################################
Apache=$(ping -c 2 $apc_IP)

if [[ $Apache == *"64 bytes from $apc_IP:"* ]]; then
        echo "SSH into server Apache"
        check=$(systemctl --host $apc status apache2)
        if [[ $check == *"active (running)"* ]]; then
                echo "Service is running."

        else
                echo "Starting services.."
#		ssh-copy-id ksd42@10.248.179.18
		ssh $apc sudo service apache2 start
                check=$(systemctl --host $apc status apache2)
                if [[ $check == *"active (running)"* ]]; then
                        echo "Service is running."
                        echo $check
                fi
        fi
else
        echo "server is down"

fi



################################## Database ################################################
Database=$(ping -c 2 $dbs_IP )

if [[ $Database == *"64 bytes from $dbs_IP:"* ]]; then
	echo "SSH into server Database"
        check=$(systemctl --host $dbs status mysql)
        if [[ $check == *"active (running)"* ]]; then
                echo "Service is running."
        else
                echo "Starting services.."
 #         	ssh-copy-id $dbs
                ssh $dbs sudo service mysql start
                check=$(systemctl --host $dbs status mysql)
                if [[ $check == *"active (running)"* ]]; then
                        echo "Service is running."
                        echo $check
                fi
        fi
else
        echo "server is down"

fi


################################## Backend ################################################
backend=$(ping -c 2 $bck_IP)

if [[ $backend == *"64 bytes from $bck_IP:"* ]]; then
        echo "SSH into server"
        check=$(systemctl --host $rbmq status rabbitmq-server.service)
        if [[ $check == *"active (running)"* ]]; then
               echo "Service is running."
	       echo "launching listener"
	       ssh adam@10.248.179.22
	       cd /home/adam/Documents/Scripts
               ./reciever.py

        fi
else
	echo "server is down"

fi

