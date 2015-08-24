require "net/http"
require 'json'

set :lab_deploy_url, 'http://deploys.dashboard.dev.scoua.de/api/v1/deploys'

after "deploy", "lab_deploy:release"

namespace :lab_deploy do
  desc "Tell Slack a new release has been deployed"
  task :release do
    if lab_deploy_url != ''
      puts '--> Lab Deploy'
      uri = URI.parse("#{lab_deploy_url}")
      data = {
        "project_name" => "#{application}",
        # "date" => Time.now.to_s,
        "deployer_name" => ENV['USER'] || ENV['USERNAME'] || 'n/a',
        "environment" => "#{stage}",
        "version" => "#{branch}",
      }
      data = JSON.parse(data.to_json)
      postData = Net::HTTP.post_form(uri, data)

      puts 'ok'
    end
  end
end