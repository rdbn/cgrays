# config valid only for current version of Capistrano
lock "3.9.0"

set :application, "cgrays"
set :repo_url, "git@github.com:rdbn/cgrays.git"
set :tmp_dir, "/tmp/cgrays"
set :deploy_to, '/var/www/cgrays'
set :linked_files, fetch(:linked_files, []).push('app/config/parameters.yml')
set :linked_dirs, fetch(:linked_dirs, []).push('var')

# Symfony console commands will use this environment for execution
set :symfony_env,  "prod"

# Set this to 2 for the old directory structure
set :symfony_directory_structure, 3
# Set this to 4 if using the older SensioDistributionBundle
set :sensio_distribution_version, 5

# symfony-standard edition directories
set :app_path, "app"
set :web_path, "web"
set :var_path, "var"
set :bin_path, "bin"

# The next 3 settings are lazily evaluated from the above values, so take care
# when modifying them
set :app_config_path, "app/config"
set :log_path, "var/logs"
set :cache_path, "var/cache"

set :composer_install_flags, '--prefer-dist --no-interaction --optimize-autoloader --quiet'

set :symfony_console_path, "bin/console"
set :symfony_console_flags, "--no-debug"

# Remove app_dev.php during deployment, other files in web/ can be specified here
set :controllers_to_clear, ["app_*.php"]

# asset management
set :assets_install_path, "web"
set :assets_install_flags,  '--symlink'

# Share files/directories between releases
set :linked_files, ["app/config/parameters.yml"]
set :linked_dirs, ["var/logs", "var/sessions", "vendor", "web/image"]

# Set correct permissions between releases, this is turned off by default
set :file_permissions_paths, ["var"]
set :permission_method, false

set :permission_method, :acl
set :file_permissions_users, ["nginx"]
set :file_permissions_paths, ["var", "web/image"]

namespace :deploy do
  task :migrate do
    on roles(:db) do
      invoke 'symfony:console', 'doctrine:migrations:migrate', '--no-interaction'
    end
  end
end

namespace :deploy do
  task :cache do
    on roles(:db) do
      invoke 'symfony:console', 'cache:clear'
    end
  end
end

set :cron_path, "#{fetch(:app_config_path)}/crontab/"+fetch(:stage).to_s
after "deploy:cleanup", "application:crontab:setup"

namespace :application do
    namespace :crontab do
        desc "Sets up crontab"
        task :setup do
            on roles (:app) do
                puts "-" * 6
                puts "Setting up crontab"
                execute :crontab, "#{release_path}/#{fetch(:cron_path)}"
                puts "-" * 6
            end
        end
    end
end

after 'deploy:updated', 'deploy:migrate'
after 'deploy:updated', 'symfony:assets:install'
after 'deploy:updated', 'deploy:cache'
