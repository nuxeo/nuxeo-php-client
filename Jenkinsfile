node('SLAVE') {
    try {
        wrap([$class: 'TimestamperBuildWrapper']) {
            stage 'checkout'
            checkout([$class: 'GitSCM', branches: [[name: '*/2.0-rewrite']], browser: [$class: 'GithubWeb', repoUrl: 'https://github.com/nuxeo/nuxeo-automation-php-client'], doGenerateSubmoduleConfigurations: false, extensions: [[$class: 'CloneOption', depth: 0, noTags: false, reference: '', shallow: false, timeout: 300]], submoduleCfg: [], userRemoteConfigs: [[url: 'git@github.com:nuxeo/nuxeo-automation-php-client.git']]])
            docker.image('quay.io/nuxeo/nuxeo-qaimage-php').pull()
            docker.build('nuxeo-qaimage-php-client', 'docker/qa').inside {
                stage 'build'
                sh 'rm -rf vendor && composer install'
                stage 'tests'
                sh 'phpunit --bootstrap vendor/autoload.php tests/Nuxeo/Tests/Client/TestNuxeoClient.php'
            }
        }
    } catch (e) {
        step([$class: 'ClaimPublisher'])
        throw e
    }
}