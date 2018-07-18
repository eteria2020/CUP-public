<?php
require 'vendor/autoload.php';
require 'RancherAPI.php';
require 'recipe/common.php';

use it\mvlabs\rancher\RancherAPI;

server('prod', '192.168.99.100', 8080)
    ->user('root')
    ->identityFile()
    ->stage('production')
    ->env('branch', 'master')
    ->env('RANCHER_URL', 'http://192.168.99.100:8080')
    ->env('RANCHER_ACCESS_KEY', 'E78E6A5E91B4D4C74EE3')
    ->env('RANCHER_SECRET_KEY', 'JM8VM9XqQgTD3qL2tdFo7yGUKNZn9dXtGev8Jc2t');

task('rancher:get_container_name', function () {
    $url = env("RANCHER_URL");
    $key = env("RANCHER_ACCESS_KEY");
    $secret = env("RANCHER_SECRET_KEY");
    
    if (!$url) {
        writeln("Missing RANCHER_URL env variable, aborting");
        die;
    }

    if (!$key) {
        writeln("Missing RANCHER_ACCESS_KEY env variable, aborting");
        die;
    }

    if (!$secret) {
        writeln("Missing RANCHER_SECRET_KEY env variable, aborting");
        die;
    }

    $client = new RancherAPI(
        $url,
        $key,
        $secret
    );

    $data = $client->getServices("kind_ne=loadBalancerService&name_prefix=sharengo");

    $count = count(array_keys($data));
    if ($count != 1) {
        writeln("Expected to get exactly 1 result from query, $count found");
        die;
    }

    env('OLD_CONTAINER_NAME', $data[0]->name);
})->desc("Check actual production service name");

task('check:revision_changed', function () {
    $old = env('OLD_CONTAINER_NAME');
    $rev = runLocally("git rev-parse HEAD");
    if ($old === "sharengo-$rev") {
        writeln("Current git revision is already running, aborting");
        die;
    }

    env('NEW_REVISION', $rev);
    
})->desc("Check that the revision is changed");

task('docker:generate_compose', function () {
    $old = env('OLD_CONTAINER_NAME');
    $rev = env('NEW_REVISION');
    
    $template = '<old-container-name>:
sharengo-<new-revision>:
  labels:
    app-name: "sharengo"
  tty: true
  restart: always
  image: docker.mvlabs.it/sharengo:<new-revision>
  stdin_open: true
';

    $content = str_replace("<old-container-name>", $old, $template);
    $content = str_replace("<new-revision>", $rev, $content);

    file_put_contents("docker-compose.yml", $content);
})->desc("Generate docker-compose.yml file");

task('docker:push_new_container', function () {
    $rev = env('NEW_REVISION');

    runLocally("docker build -t docker.mvlabs.it/sharengo:$rev .");
    runLocally("docker push docker.mvlabs.it/sharengo");
})->desc("Prepare and push the new docker container");

task('rancher:compose', function () {
    $old = env('OLD_CONTAINER_NAME');
    $rev = env('NEW_REVISION');
    
    $url = env("RANCHER_URL");
    $key = env("RANCHER_ACCESS_KEY");
    $secret = env("RANCHER_SECRET_KEY");
    
    $env = "RANCHER_URL=$url RANCHER_ACCESS_KEY=$key RANCHER_SECRET_KEY=$secret";
    
    runLocally("$env rancher-compose --verbose -p Default upgrade -w -c $old sharengo-$rev");
})->desc("Executing rancher compose");
    
task('deploy', [
    'rancher:get_container_name',
    'check:revision_changed',
    'docker:generate_compose',
    'docker:push_new_container',
    'rancher:compose'
])->desc('Deploy your project');
