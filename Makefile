.PHONY: test
test:
	phpunit --bootstrap vendor/autoload.php tests/Nuxeo/Tests/Automation/Client/TestNuxeoClient.php
