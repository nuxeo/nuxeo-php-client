
![Packagist Version](https://img.shields.io/packagist/v/nuxeo/nuxeo-php-client)
![Packagist Downloads](https://img.shields.io/packagist/dt/nuxeo/nuxeo-php-client)
![GitHub](https://img.shields.io/github/license/nuxeo/nuxeo-php-client)

[![Dependencies checks](https://github.com/nuxeo/nuxeo-php-client/actions/workflows/dependencies_check.yml/badge.svg)](https://github.com/nuxeo/nuxeo-php-client/actions/workflows/dependencies_check.yml)
[![Unit tests](https://github.com/nuxeo/nuxeo-php-client/actions/workflows/unit_tests.yml/badge.svg)](https://github.com/nuxeo/nuxeo-php-client/actions/workflows/unit_tests.yml)
[![Functional tests](https://github.com/nuxeo/nuxeo-php-client/actions/workflows/ftests.yml/badge.svg)](https://github.com/nuxeo/nuxeo-php-client/actions/workflows/ftests.yml)
[![Integration tests](https://github.com/nuxeo/nuxeo-php-client/actions/workflows/integration_tests.yml/badge.svg)](https://github.com/nuxeo/nuxeo-php-client/actions/workflows/integration_tests.yml)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=org.nuxeo%3Anuxeo-php-client&metric=alert_status)](https://sonarcloud.io/dashboard?id=org.nuxeo%3Anuxeo-php-client)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=org.nuxeo%3Anuxeo-php-client&metric=security_rating)](https://sonarcloud.io/dashboard?id=org.nuxeo%3Anuxeo-php-client)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=org.nuxeo%3Anuxeo-php-client&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=org.nuxeo%3Anuxeo-php-client)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=org.nuxeo%3Anuxeo-php-client&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=org.nuxeo%3Anuxeo-php-client)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=org.nuxeo%3Anuxeo-php-client&metric=coverage)](https://sonarcloud.io/dashboard?id=org.nuxeo%3Anuxeo-php-client)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=org.nuxeo%3Anuxeo-php-client&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=org.nuxeo%3Anuxeo-php-client)

# Nuxeo PHP Client

The Nuxeo PHP Client is a PHP client library for Nuxeo Rest API.

This is supported by Nuxeo and compatible with Nuxeo LTS 2015 and latest Fast Tracks.

# Code

## Requirements

 * ![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/nuxeo/nuxeo-php-client)
 * [Composer](https://getcomposer.org/)

Stuck with an old PHP version ? Have a look at [v1.5](https://github.com/nuxeo/nuxeo-php-client/tree/1.5), it offers limited but effective support and requires PHP 5.3+

## Getting Started

### Server

- [Download a Nuxeo server](http://www.nuxeo.com/en/downloads) (the zip version)

- Unzip it

- Linux/Mac:
    - `NUXEO_HOME/bin/nuxeoctl start`
- Windows:
    - `NUXEO_HOME\bin\nuxeoctl.bat start`

- From your browser, go to `http://localhost:8080/nuxeo`

- Follow Nuxeo Wizard by clicking 'Next' buttons, re-start once completed

- Check Nuxeo correctly re-started `http://localhost:8080/nuxeo`
  - username: Administrator
  - password: Administrator

### Library import

Download the latest build [Nuxeo PHP Client main](https://github.com/nuxeo/nuxeo-php-client/archive/main.zip).

Download the latest stable ![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/nuxeo/nuxeo-php-client).

Composer:

```
  "require": {
    "nuxeo/nuxeo-php-client": "~2.0"
  }
```

### Usage

#### Creating a Client

The following documentation and samples applies for the 1.5 and newer versions. Calls to the Automation API for previous versions of the client will require adjustments.

For a given `url`:

```php
$url = 'http://localhost:8080/nuxeo';
```

And given credentials:

```php
use Nuxeo\Client\NuxeoClient;

$client = new NuxeoClient($url, 'Administrator', 'Administrator');
```

##### Options

Options can be set on client or API objects. This ensure inheritance and isolation of options on the object whose options are applied. As it, the client gives its options to API objects.

```php
// To define global schemas, global enrichers and global headers in general
$client = $client->schemas("dublincore", "common")
  ->enrichers('document', ['acls'])
```

```php
// For defining all schemas
$client = $client->schemas("*");
```

```php
// For changing authentication method

use Nuxeo\Client\Auth\PortalSSOAuthentication;
use Nuxeo\Client\Auth\TokenAuthentication;

// PortalSSOAuthentication with nuxeo-platform-login-portal-sso
$client = $client->setAuthenticationMethod(new PortalSSOAuthentication($secret, $username));

// TokenAuthentication
$client = $client->setAuthenticationMethod(new TokenAuthentication($token));

// OAuth2Authentication
// The PHP client doesn't implement OAuth2 authorization flow as 
// it depends completely on the architecture choices of your app.
// To help understanding and implement, please find a sample SF4 app under intergation/oauth2.
// Once the authorization flow is ready and you have an access token,
// you can use the OAuth2Authentication in the PHP client:
$client = $client->setAuthenticationMethod(new OAuth2Authentication($accessToken));
```

#### APIs

##### Automation API

To use the Automation API, `Nuxeo\Client\NuxeoClient#automation()` is the entry point for all calls:

```php
// Fetch the root document
$result = $client->automation('Repository.GetDocument')->param("value", "/")->execute();
// Type auto-detected and cast as Nuxeo\Client\Objects\Document
```

```php
// Execute query
$operation = $client->automation('Repository.Query')->param('query', 'SELECT * FROM Document');
$result = $operation->execute();
// Type auto-detected and cast as Nuxeo\Client\Objects\Documents
```

```php
use Nuxeo\Client\Objects\Blob\Blob;
use Nuxeo\Client\Objects\Blob\Blobs;

// To upload|download blob(s)

$fileBlob = Blob::fromFile('/local/file.txt', 'text/plain');
$blob = $client->automation('Blob.AttachOnDocument')->param('document', '/folder/file')->input($fileBlob)->execute(Blob::class);

$inputBlobs = new Blobs();
$inputBlobs->add('/local/file1.txt', 'text/plain');
$inputBlobs->add('/local/file2.txt', 'text/plain');
$blobs = $client->automation('Blob.AttachOnDocument')->param('xpath', 'files:files')->param('document', '/folder/file')->input($inputBlobs)->execute(Blobs::class);

$resultBlob = $client->automation('Document.GetBlob')->input('folder/file')->execute(Blob::class);
```

```php
use Nuxeo\Client\Objects\Document;

class MyBusinessClass extends Nuxeo\Client\Objects\Document {
      ...
}

// Unserialize document in a custom class
$operation = $client->automation('Document.Fetch')->param('value', '0fa9d2a0-e69f-452d-87ff-0c5bd3b30d7d');
$result = $operation->execute(MyBusinessClass::class);
```

```php
use Nuxeo\Client\Objects\Document;
use Nuxeo\Client\Objects\Operation\DocRef;

// Enforce type of a property
$doc = $client->automation('Document.Fetch')->param('value', '0fa9d2a0-e69f-452d-87ff-0c5bd3b30d7d')->execute(Document::class);
$property = $doc->getProperty('custom:related', DocRef::class);
```

##### Repository API

```php
// Fetch the root document
$document = $client->repository()->fetchDocumentRoot();
```

```php
// Fetch document by path
$document = $client->repository()->fetchDocumentByPath('/folders_2');
```

```php
// Create a document
$document = Objects\Document::create()
  ->setProperty('dc:title', 'Some title');
```

```php
// Update a document
$repository = $client->repository(); 
$document = $repository->fetchDocumentByPath('/note_0');
document->setPropertyValue("dc:title", "note updated");
$repository->updateDocumentByPath('/note_0', $document);
```

```php
// Delete a document
$client->repository()->deleteDocumentByPath('/note_2');
```

##### Users/Groups

```php
// Get current user used to connect to Nuxeo Server
/** @var \Nuxeo\Client\Objects\User\User $user */
$user = $client->userManager()->fetchCurrentUser();
```

```php
// Create User
$userManager->createUser((new User())
      ->setUsername('my_login')
      ->setCompany('Nuxeo')
      ->setEmail('user@company.com')
      ->setFirstName('Thomas A.')
      ->setLastName('Anderson')
      ->setPassword('passw0rd'));
```

```php
//Update user
$userManager->updateUser($user);
```

```php
//Attach user to group
$userManager->attachGroupToUser('username', 'group_name');
$userManager->attachUserToGroup('group_name', 'username');
```

##### Workflows

```php
// Fetch current user workflow tasks
/** @var \Nuxeo\Client\Objects\Workflow\Tasks $tasks */
$tasks = $client->workflows()->fetchTasks();
```

#### Errors/Exceptions

The main exception type is `Nuxeo\Client\Spi\NuxeoClientException` and contains:

- The HTTP error status code (666 for internal errors)

- An info message

## Docker

We provide a [docker-compose.yml](https://github.com/nuxeo/nuxeo-php-client/blob/master/docker-compose.yml) for quick testing

Just install docker-compose and run `docker-compose up`, you'll have a nuxeo running on http://localhost:9081/ and nginx on http://localhost:9080/

You can access the samples with http://localhost:9080/samples/B1.php for example.

# Contributing / Reporting issues

We are glad to welcome new developers, and even simple usage feedback is great

 * Ask your questions on http://answers.nuxeo.com/
 * Report issues on this GitHub repository (see [issues link](https://github.com/nuxeo/nuxeo-php-client/issues) on the right)

# License

[Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0.html)

# About Nuxeo

The [Nuxeo Platform](http://www.nuxeo.com/products/content-management-platform/) is an open source customizable and extensible content management platform for building business applications. It provides the foundation for developing [document management](http://www.nuxeo.com/solutions/document-management/), [digital asset management](http://www.nuxeo.com/solutions/digital-asset-management/), [case management application](http://www.nuxeo.com/solutions/case-management/) and [knowledge management](http://www.nuxeo.com/solutions/advanced-knowledge-base/). You can easily add features using ready-to-use addons or by extending the platform using its extension point system.

The Nuxeo Platform is developed and supported by Nuxeo, with contributions from the community.

Nuxeo dramatically improves how content-based applications are built, managed and deployed, making customers more agile, innovative and successful. Nuxeo provides a next generation, enterprise ready platform for building traditional and cutting-edge content oriented applications. Combining a powerful application development environment with
SaaS-based tools and a modular architecture, the Nuxeo Platform and Products provide clear business value to some of the most recognizable brands including Verizon, Electronic Arts, Sharp, FICO, the U.S. Navy, and Boeing. Nuxeo is headquartered in New York and Paris.
More information is available at [www.nuxeo.com](http://www.nuxeo.com).
