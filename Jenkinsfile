/*
 * (C) Copyright 2016 Nuxeo SA (http://nuxeo.com/) and contributors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Contributors:
 *     Pierre-Gildas MILLON <pgmillon@nuxeo.com>
 */

node('SLAVE') {
    timeout(60) {
        timestamps {
            try {
                tool type: 'hudson.model.JDK', name: 'java-8-openjdk'
                tool type: 'hudson.tasks.Maven$MavenInstallation', name: 'maven-3'

                docker.image('dockerin.nuxeo.com/nuxeo/nuxeo-qaimage-php:php53-cli').pull()
                String ctnrId = sh(script:"cat /proc/self/cgroup | cut -d : -f3 | grep -oe '[0-9a-fA-F]\\{12,\\}' | head -1 || true", returnStdout: true).trim()

                stage('checkout') {
                    checkout scm
                }

                def dockerImage = docker.build('nuxeo-qaimage-php-client', 'docker/qa')
                dockerImage.inside {
                    stage('install dependencies') {
                        sh """#!/bin/bash -ex
                            curl -sS https://getcomposer.org/installer | php
                            php composer.phar install
                        """
                    }
                    stage('dependencies vulnerability check') {
                        sh 'php vendor/bin/security-checker security:check --end-point=http://security.sensiolabs.org/check_lock'
                    }
                    stage('unit tests') {
                        sh 'php vendor/bin/phpunit --exclude-group server'
                    }
                }
                dockerImage.withRun("-v ${env.WORKSPACE}:${env.WORKSPACE} -w ${env.WORKSPACE} --link ${ctnrId}:nuxeo", 'tail -f /dev/null') { c ->
                    stage('ftests') {
                        writeFile file: 'bin/php', text: """#!/usr/bin/env bash
                            set -ex
                            docker exec -u jenkins ${c.id} php \$@
                        """
                        sh """#!/usr/bin/env bash
                            chmod +x bin/php
                            mvn -f ftests/pom.xml clean verify
                        """
                    }

                }
            } finally {
//                claimPublisher()
            }

        }
    }
}
