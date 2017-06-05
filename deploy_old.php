<?php
namespace Deployer;

// Configuration

set('repository', 'https://mastercad@bitbucket.org/AmbitiousTeam/trainingsmanager.git');
set('git_tty', true); // [Optional] Allocate tty for git on first deployment
add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', ['/data/tmp', '/public/tmp', '/public/images/content/dynamisch']);

// Hosts

host('trainingsmanager')
    ->stage('production')
    ->set('deploy_path', '/html/trainingsmanager');

// Tasks

//desc('Restart PHP-FPM service');
//task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
//    run('sudo systemctl restart php-fpm.service');
//});

//after('deploy:symlink', 'php-fpm:restart');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
