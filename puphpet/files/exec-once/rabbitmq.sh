echo deb http://www.rabbitmq.com/debian/ testing main > /etc/apt/sources.list.d/rabbitmq.list
wget https://www.rabbitmq.com/rabbitmq-signing-key-public.asc
apt-key add rabbitmq-signing-key-public.asc
apt-get update && apt-get install rabbitmq-server
rabbitmqctl add_user test test
rabbitmqctl set_user_tags test management
rabbitmqctl set_permissions -p / test ".*" ".*" ".*"