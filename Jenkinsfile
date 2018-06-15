/*
 * (C) Copyright 2018 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 */

properties([
    [$class: 'BuildDiscarderProperty', strategy: [$class: 'LogRotator', daysToKeepStr: '60', numToKeepStr: '60', artifactNumToKeepStr: '1']],
    disableConcurrentBuilds(),
    [$class: 'RebuildSettings', autoRebuild: false, rebuildDisabled: false],
])

node('SLAVE') {
  timeout(60) {
    timestamps {
      try {
        tool type: 'hudson.model.JDK', name: 'java-8-openjdk'
        tool type: 'hudson.tasks.Maven$MavenInstallation', name: 'maven-3'
        def composer = 'php composer.phar'
        def phpunit = 'php vendor/bin/phpunit'

        def dockerImage = docker.image('php:cli')
        String ctnrId = sh(script:"cat /proc/self/cgroup | cut -d : -f3 | grep -oe '[0-9a-fA-F]\\{12,\\}' | head -1 || true", returnStdout: true).trim()

        dockerImage.pull()

        stage('checkout') {
          checkout scm
          sh 'git clean -fdx' // JENKINS-31924 fixed with git-plugin v2+

          sh 'curl -sS https://getcomposer.org/installer | php'
        }
        dockerImage.withRun("-v ${env.WORKSPACE}:${env.WORKSPACE} -w ${env.WORKSPACE} --link ${ctnrId}:nuxeo", 'tail -f /dev/null') { c ->
          withEnv(["PATH+BIN=${env.WORKSPACE}/bin"]) {
            writeFile file: 'bin/php', text: """#!/usr/bin/env bash
                set -ex
                docker exec -u 1001:1001 ${c.id} php \$@
            """

            sh """
              chmod +x bin/php
              docker exec ${c.id} bash -c 'apt-get update && apt-get install -y unzip'"""

            stage('install dependencies') {
              sh "${composer} install"
            }
            stage('dependencies vulnerability check') {
              sh 'php vendor/bin/security-checker security:check'
            }
            stage('dependencies outdated check') {
              try {
                echo """
Outdated dependencies Report
~~~~~~~~~~~~~~~~~~~~~~~~~~~~"""
                sh "${composer} outdated -D --strict"
              } catch (ignore) {
                echo 'There are outdated dependencies, marking the build as Unstable.'
                currentBuild.result = 'UNSTABLE'
              }
            }
            stage('unit tests') {
              sh "${phpunit} --exclude-group server"
            }
            stage('ftests') {
              sh "mvn -f ftests/pom.xml clean verify"
            }

          }
        }
      } catch (e) {
        currentBuild.result = "FAILURE"
        step([$class: 'ClaimPublisher'])
        throw e
      } finally {
        step([$class: 'JiraIssueUpdater', issueSelector: [$class: 'DefaultIssueSelector'], scm: scm])
      }
    }
  }
}
