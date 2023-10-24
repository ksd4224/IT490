#!/bin/bash

##Change the IP's here the rest of the script will adjust appropriately##
rbmq=nikitas@10.248.179.18
rbmq_IP=10.248.179.18
apc=ksd42@10.248.179.22
apc_IP=10.248.179.22
dbs=rp54@10.248.179.10
dbs_IP=10.248.179.10
bck=adam@10.248.179.6
bck_IP=10.248.179.6
################################## RabbitMQ ################################################
rabbitmq=$(ping -c 2 $rbmq_IP)

if [[ $rabbitmq == *"64 bytes from $rbmq_IP:"* ]]; then
	echo "Checking Status"
	check=$(ssh $rbmq sudo service rabbitmq-server status)

	if [[ $check == *"active (running)"* ]]; then
		echo "Service is running"

	else
		#ssh-copy-id $rbmq
		echo "Starting services.."
		ssh $rbmq sudo service rabbitmq-server start
		check=$(ssh $rbmq sudo service rabbitmq-server status)
	        if [[ $check == *"active (running)"* ]]; then
       	        	echo "Service is running"
			echo $check
		fi
	fi
else
	echo "rabbitmq server is down"

fi

################################## Apache ################################################
servstat=$(sudo service apache2 status)

if [[ $servstat == *"active (running)"* ]]; then
	echo "Already Running"
else
	echo "Starting Apache2..."
	systemctl start apache2
fi

################################## Database ################################################
Database=$(ping -c 2 $dbs_IP )

if [[ $Database == *"64 bytes from $dbs_IP:"* ]]; then
        echo "SSH into server"
        check=$(systemctl --host $dbs status mysql)
        if [[ $check == *"active (running)"* ]]; then
                echo "Service is running."
        else
                echo "Starting services.."
              	ssh-copy-id $dbs
                ssh $dbs sudo service mysql start
                check=$(systemctl --host $dbs status mysql)
                if [[ $check == *"active (running)"* ]]; then
                        echo "Service is running."
                        echo $check
                fi
        fi
else
        echo "db server is down"

fi


################################## Backend ################################################
backend=$(ping -c 2 $bck_IP)

if [[ $backend == *"64 bytes from $bck_IP:"* ]]; then
        echo "SSH into server"
        check=$(systemctl --host $rbmq status rabbitmq-server.service)
        if [[ $check == *"active (running)"* ]]; then
        	echo "Service is running."
		echo "launching listener"
		cd /home/adam/Documents/Scripts
		./reciever.py
        fi
else
        echo "backend server is down"

fi

