#!/bin/bash

##Change the IP's here the rest of the script will adjust appropriately##
#nikitas section
primary_rbmq=nikitas@10.248.179.10
primary_rbmq_IP=10.248.179.10
secondary_apc=nikitas@10.248.179.10
secondary_apc_IP=10.248.179.10

#keyas section
primary_apc=keya@10.248.179.14
primary_apc_IP=10.248.179.14
secondary_rbmq=keya@10.248.179.14
secondary_rbmq_IP=10.248.179.14

#Ritiks section
priamry_dbs=rp54@10.248.179.18
primary_dbs_IP=10.248.179.18
tertiary_rbmq=rp54@10.248.179.18
tertiary_rbmq_IP=10.248.179.18


#adams section
bck=adam@10.248.179.22
bck_IP=10.248.179.22
secondary_dbs=adam@10.248.179.22
secondary_dbs=10.248.179.22


################################## Primary_RabbitMQ ################################################
primary_rabbitmq=$(ping -c 2 $primary_rbmq_IP)
secondary_rabbitmq=$(ping -c 2 $secondary_rbmq_IP)
tertiary_rabbitmq=$(ping -c 2 $tertiary_rbmq_IP)


if [[ $primary_rabbitmq == *"64 bytes from $primary_rbmq_IP:"* ]]; then
	echo "Checking Status"
	check=$(systemctl --host $primary_rbmq status rabbitmq-server.service)
	if [[ $check == *"active (running)"* ]]; then
		echo "Service is running"

	else
#		ssh-copy-id nikitas@10.248.179.10
		echo "SSH into rabbitmq"
		ssh $primary_rbmq sudo service rabbitmq-server start
		check=$(systemctl --host $primary_rbmq status rabbitmq-server.service)
	        if [[ $check == *"active (running)"* ]]; then
       	        	echo "Service is running"
			echo $check
		fi
	fi
elif [[ $secondary_rabbitmq == *"64 bytes from $secondary_rbmq_IP:"* ]]; then
	echo "Checking Status"
	check=$(systemctl --host $secondary_rbmq status rabbitmq-server.service)
	if [[ $check == *"active (running)"* ]]; then
		echo "Service is running"

	else
#		ssh-copy-id keya@10.248.179.10
		echo "SSH into secondary rabbitmq"
		ssh $secondary_rbmq sudo service rabbitmq-server start
		check=$(systemctl --host $secondary_rbmq status rabbitmq-server.service)
	        if [[ $check == *"active (running)"* ]]; then
       	        	echo "Service is running"
			echo $check

elif [[ $tertiary_rabbitmq == *"64 bytes from $tertiary_rbmq_IP:"* ]]; then
	echo "Checking Status"
	check=$(systemctl --host $tertiary_rbmq status rabbitmq-server.service)
	if [[ $check == *"active (running)"* ]]; then
		echo "Service is running"

	else
#		ssh-copy-id rp54@10.248.179.10
		echo "SSH into secondary rabbitmq"
		ssh $tertiary_rbmq sudo service rabbitmq-server start
		check=$(systemctl --host $tertiary_rbmq status rabbitmq-server.service)
	        if [[ $check == *"active (running)"* ]]; then
       	        	echo "Service is running"
			echo $check
else
	echo "No rabbitmq servers are running at this time. What are the odds\!?.... 3.. the odds are 3 out of 3."
fi

################################## Apache ################################################
primary_Apache=$(ping -c 2 $primary_apc_IP)
secondary_Apache=$(ping -c 2 $secondary_apc_IP)

if [[ $primary_Apache == *"64 bytes from $primary_apc_IP:"* ]]; then
        echo "SSH into server Apache"
        check=$(systemctl --host $primary_apc status apache2)
        if [[ $check == *"active (running)"* ]]; then
                echo "Service is running."

        else
                echo "Starting services.."
#		ssh-copy-id rp54@10.248.179.18
		ssh $primary_apc sudo service apache2 start
                check=$(systemctl --host $primary_apc status apache2)
                if [[ $check == *"active (running)"* ]]; then
                        echo "Service is running."
                        echo $check
                fi
        fi

elif [[ $secondary_Apache == *"64 bytes from $secondary_apc_IP:"* ]]; then
        echo "SSH into server Apache"
        check=$(systemctl --host $secondary_apc status apache2)
        if [[ $check == *"active (running)"* ]]; then
                echo "Service is running."

        else
                echo "Starting services.."
#		ssh-copy-id rp54@10.248.179.18
		ssh $secondary_apc sudo service apache2 start
                check=$(systemctl --host $secondary_apc status apache2)
                if [[ $check == *"active (running)"* ]]; then
                        echo "Service is running."
                        echo $check
                fi
        fi

else
        echo "No Apache servers running at this time. What are the odds\!?..... 2 .. the odds are 2 out of 2."

fi



################################## Database ################################################
primary_Database=$(ping -c 2 $primary_dbs_IP )
secondary_Database=$(ping -c 2 $secondary_dbs_IP)

if [[ $primary_Database == *"64 bytes from $primary_dbs_IP:"* ]]; then
	echo "SSH into server Database"
        check=$(systemctl --host $primary_dbs status mysql)
        if [[ $check == *"active (running)"* ]]; then
                echo "Service is running."
        else
                echo "Starting services.."
 #         	ssh-copy-id $dbs
                ssh $primary_dbs sudo service mysql start
                check=$(systemctl --host $primary_dbs status mysql)
                if [[ $check == *"active (running)"* ]]; then
                        echo "Service is running."
                        echo $check
                fi
        fi
elif [[ $secondary_Database == *"64 bytes from $secondary_dbs_IP:"* ]]; then
	echo "SSH into server Database"
        check=$(systemctl --host $secondary_dbs status mysql)
        if [[ $check == *"active (running)"* ]]; then
                echo "Service is running."
        else
                echo "Starting services.."
 #         	ssh-copy-id $dbs
                ssh $secondary_dbs sudo service mysql start
                check=$(systemctl --host $secondary_dbs status mysql)
                if [[ $check == *"active (running)"* ]]; then
                        echo "Service is running."
                        echo $check
                fi
        fi
else
        echo "No Database servers running at this time. What are the odds\!?..... 2 .. the odds are 2 out of 2."

fi


################################## Backend ################################################
backend=$(ping -c 2 $bck_IP)
check_one=$(systemctl --host $primary_rbmq status rabbitmq-server.service)
check_two=$(systemctl --host $secondary_rbmq status rabbitmq-server.service)
check_three=$(systemctl --host $tertiary_rbmq status rabbitmq-server.service)

file_reg="/home/adam/Projects/Project490/receiver.py"
file_login="/home/adam/Projects/Project490/login.py"

if [[ check_one == *"active (running)"* ]]; then
	new_host = $primary_rbmq_IP
	sed -i "s/\(parameters = pika.ConnectionParameters(host='\)[^']*\(.*\)/\1$new_host\2/" $file_reg

elif [[ check_two == *"active (running)"* ]]; then
	new_host = $secondary_rbmq_IP
	sed -i "s/\(parameters = pika.ConnectionParameters(host='\)[^']*\(.*\)/\1$new_host\2/" $file_reg

elif [[ check_three == *"active (running)"* ]]; then
	new_host = $tertiary_rbmq_IP
	sed -i "s/\(parameters = pika.ConnectionParameters(host='\)[^']*\(.*\)/\1$new_host\2/" $file_reg

else
	echo "No longer want to do their jobs... what a shame."

fi

