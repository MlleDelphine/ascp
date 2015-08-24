set :deploy_to,   "/var/www/recette.service-civique.gouv.dev.scoua.de"
set :domain,      "recette.service-civique.gouv.dev.scoua.de"
set :branch,      "master"

ssh_options[:user] = 'capistrano'

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set :php_bin, "/opt/php55/bin/php"

