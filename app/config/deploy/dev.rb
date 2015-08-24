set :deploy_to,   "/var/www/new.service-civique.gouv.dev.scoua.de"
set :domain,      "new.service-civique.gouv.dev.scoua.de"
set :clear_controllers, false

ssh_options[:user] = 'capistrano'

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set :branch, "dev"

set :php_bin, "/opt/php55/bin/php -d memory_limit=-1"

set :apc_clear_username, 'apc'
set :apc_clear_password, 'Dzg2Nvcx2'

after "deploy:finalize_update" do
    capifony_pretty_print "--> Edit APC key (add _dev suffix)"
    run "cd #{release_path}/web && sed -i.bak 's/service_civique_website/service_civique_website_dev/' app.php"
    capifony_puts_ok
end
