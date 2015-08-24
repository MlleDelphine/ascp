set :stages, %w(dev preprod prod)
set :default_stage, "dev"
set :stage_dir,     "app/config/deploy"

require 'capistrano/ext/multistage'
require 'net/http'
require 'uri'
require 'json'

set :application, "service-civique-website"
set :app_path,    "app"

set :repository,  "git@github.com:LaNetscouade/#{application}.git"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :apc_clear_username, 'username'
set :apc_clear_password, 'password'


# lab_deploy
set :deploy_dir, "#{File.dirname(__FILE__)}"
load deploy_dir + '/deploy/includes/lab_deploy'
set :lab_deploy_url, "http://deploys.dashboard.dev.scoua.de/api/v1/deploys"


set :shared_files,      ["app/config/parameters.yml", "apc_reset.ini"]
set :shared_children,   [app_path + "/sessions", app_path + "/logs", web_path + "/uploads", "exports"]

# SSH options
set :ssh_options, { :forward_agent => true }

# Composer
set :use_composer, true
set :update_vendors, false
set :composer_options,  "--no-dev --verbose --prefer-dist --optimize-autoloader --no-progress"

set :model_manager, "doctrine" # Or: `propel`

set  :use_sudo,      false
set  :keep_releases,  3

set  :deploy_via, :remote_cache

# Be more verbose by uncommenting the following line
# logger.level = Logger::MAX_LEVEL

namespace :assets do
  task :install, :except => { :no_release => true } do
    capifony_pretty_print "--> Build assets locally (gulp)"
    run_locally "gulp package";
    capifony_puts_ok
    capifony_pretty_print "--> Upload assets"
    top.upload "tmp/assets.tar.gz", release_path + '/', :via => :scp
      run "cd #{release_path} && tar -xzf assets.tar.gz && rm -rf assets.tar.gz"
    capifony_puts_ok
  end
end

namespace :symfony do
  desc "Clear apc cache"
  task :clear_apc, :roles => :web do
    capifony_pretty_print "--> APC clear"
    puts '';
    find_servers_for_task(current_task).each do |current_server|
      capifony_pretty_print "- #{current_server}"

      uri = URI.parse("http://#{current_server.host}:#{current_server.port}/apc_reset.php")

      Net::HTTP.start(uri.host, uri.port) do |http|

        request = Net::HTTP::Get.new uri.request_uri
        request.basic_auth apc_clear_username, apc_clear_password

        response = http.request request # Net::HTTPResponse object

        result = JSON.parse(response.body)

        if !result['success']
          warn result['message']
        else
          capifony_puts_ok
        end
      end
    end
  end
end

namespace :new_relic do
  desc "Clear apc cache"
  task :notify, :roles => :web do
    capifony_pretty_print "--> New relic notification"
  end
end


namespace :symfony do
  desc "Import translation"
  task :import_translations, :roles => :app, :except => { :no_release => true } do
    capifony_pretty_print "--> Import translation"
    run "#{try_sudo} sh -c 'cd #{release_path} && #{php_bin} #{symfony_console} lexik:translations:import ServiceCiviqueWebBundle --env=#{symfony_env_prod}'"
    run "#{try_sudo} sh -c 'cd #{release_path} && #{php_bin} #{symfony_console} lexik:translations:import ServiceCiviqueUserBundle --env=#{symfony_env_prod}'"
    capifony_puts_ok
  end
  task :copy_htaccess, :roles => :app, :except => {  :no_release => true } do
    run "cp #{previous_release}/web/.htaccess #{release_path}/web/"
  end
  task :dump_js_routing, :roles => :app, :except => {  :no_release => true } do
    run "#{try_sudo} sh -c 'cd #{release_path} && #{php_bin} #{symfony_console} fos:js-routing:dump --env=#{symfony_env_prod}'"
  end
end

namespace :sitemap do
  desc "Generate sitemap"
  task :generate, :roles => :app, :except => { :no_release => true } do
    capifony_pretty_print "--> Generate sitemap"
    run "#{try_sudo} sh -c 'cd #{release_path} && #{php_bin} #{symfony_console} sonata:seo:sitemap web #{domain} --env=#{symfony_env_prod}'"
    capifony_puts_ok
  end
end

namespace :deploy do
  task :migrate do
    invoke 'symfony:console', 'doctrine:migrations:migrate', '--no-interaction'
  end
end

after "deploy:update_code" do
  symfony.copy_htaccess
  symfony.dump_js_routing
  #symfony.doctrine.cache.clear_metadata
  symfony.doctrine.migrations.migrate
  assets.install
  sitemap.generate
  symfony.import_translations
end

after "deploy:update", "deploy:cleanup"

# after "deploy", "symfony:clear_apc"
after "deploy:rollback:cleanup", "symfony:clear_apc"
