set :deploy_to,   "/var/www/service-civique.gouv.fr/"

set :url,          "www.service-civique.gouv.fr"

set :domain,      '31.15.25.69'

set :web1,         url + ":8001"
set :web2,         url + ":8002"

set :php_bin, "php -d memory_limit=1G"

set :branch,      "master"

set :apc_clear_username, 'apc'
set :apc_clear_password, 'Dzg2Nvcx0'

ssh_options[:user] = 'srvcvq'
ssh_options[:port] = 2221

role :web, web1, :no_release => true
role :web, web2, :no_release => true

role :app, domain, :primary => true       # This may be the same as your `Web` server

