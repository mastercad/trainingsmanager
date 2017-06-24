<?php
namespace Deployer;

require 'recipe/zend_framework.php';

// Configuration

set('repository', 'https://mastercad@bitbucket.org/AmbitiousTeam/trainingsmanager.git');
set('git_tty', true); // [Optional] Allocate tty for git on first deployment
add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);


// Hosts

# 8C8tt65b

host('trainingsmanager.org')
    ->hostname('alfa3054.ssh.alfahosting.de')
    ->user('"web24978887@alfa3054"')
    ->multiplexing(false)
//    ->password('8C8tt65b')
    ->stage('production')
    ->set('deploy_path', 'html/trainingsmanager');

//host('trainingsmanager.org')
//host('"web24978887@alfa3054"@alfa3054.ssh.alfahosting.de')
//    ->stage('production')
//    ->set('deploy_path', 'html/trainingsmanager');
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
