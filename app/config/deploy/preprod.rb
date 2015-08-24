set :deploy_to,   "/var/www/service-civique.gouv.fr/"
set :domain,      "www.service-civique.gouv.preprod.scoua.de"
set :branch,      "master"

ssh_options[:user] = 'ssh_srvcvq'

role :web,        domain + ':81', :no_release => true               # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set :php_bin, "php -d memory_limit=-1"

set :apc_clear_username, 'apc'
set :apc_clear_password, 'Dzg2Nvcx1'

