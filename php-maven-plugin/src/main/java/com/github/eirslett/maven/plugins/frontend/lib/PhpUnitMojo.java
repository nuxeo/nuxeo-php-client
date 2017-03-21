/*
 * (C) Copyright 2017 Nuxeo SA (http://nuxeo.com/) and contributors.
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
 */

package com.github.eirslett.maven.plugins.frontend.lib;

import org.apache.maven.plugin.AbstractMojo;
import org.apache.maven.plugin.MojoExecution;
import org.apache.maven.plugin.MojoExecutionException;
import org.apache.maven.plugin.MojoFailureException;
import org.apache.maven.plugins.annotations.Component;
import org.apache.maven.plugins.annotations.LifecyclePhase;
import org.apache.maven.plugins.annotations.Mojo;
import org.apache.maven.plugins.annotations.Parameter;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;

@Mojo(name = "phpunit", defaultPhase = LifecyclePhase.TEST)
public class PhpUnitMojo extends AbstractMojo {

    private final Logger log = LoggerFactory.getLogger(getClass());

    @Component
    protected MojoExecution execution;

    /**
     * Whether you should skip while running in the test phase (default is false)
     */
    @Parameter(property = "skipTests", required = false, defaultValue = "false")
    protected Boolean skipTests;

    /**
     * The base directory for running all Node commands. (Usually the directory that contains package.json)
     */
    @Parameter(defaultValue = "${basedir}", property = "workingDirectory", required = false)
    protected File workingDirectory;

    /**
     * Determines if this execution should be skipped.
     */
    private boolean skipTestPhase() {
        return skipTests && isTestingPhase();
    }

    /**
     * Determines if the current execution is during a testing phase (e.g., "test" or "integration-test").
     */
    private boolean isTestingPhase() {
        String phase = execution.getLifecyclePhase();
        return "test".equals(phase) || "integration-test".equals(phase);
    }

    public void execute() throws MojoExecutionException, MojoFailureException {
        if(!(skipTestPhase())) {
            ProcessExecutor processExecutor = new ProcessExecutor(
                    workingDirectory,
                    new ArrayList<String>(),
                    new ArrayList<String>(),
                    Platform.guess(),
                    new HashMap<String, String>());

            try {
                final int result = processExecutor.executeAndRedirectOutput(log);
                if(result != 0) {
                    throw new MojoFailureException("Failed to run phpunit (error code " + result + ")");
                }
            } catch (ProcessExecutionException e) {
                throw new MojoExecutionException("Failed to run phpunit", e);
            }
        } else {
            getLog().info("Skipping execution.");
        }
    }

}
