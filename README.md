[![packagist](https://img.shields.io/packagist/v/telesign/telesign.svg)](https://packagist.org/packages/telesign/telesign) [![license](https://img.shields.io/github/license/TeleSign/php_telesign.svg)](https://github.com/TeleSign/php_telesign/blob/master/LICENSE)

# Telesign Self-service PHP SDK

[Telesign](https://telesign.com) connects, protects, and defends the customer experience with intelligence from billions of digital interactions and mobile signals. Through developer-friendly APIs that deliver user verification, digital identity, and omnichannel communications, we help the world's largest brands secure onboarding, maintain account integrity, prevent fraud, and streamline omnichannel engagement.

## Requirements

* **PHP 7.2+**.
* **Composer** *(optional)* - This package manager isn't required to use this SDK, but it is required to use the installation instructions below.  

> **NOTE:**
> 
> These instructions are for MacOS. They will need to be adapted if you are installing on Windows.

## Installation

Follow these steps to add this SDK as a dependency to your project.

1. *(Optional)* Create a new directory for your PHP project. Skip this step if you already have created a project. If you plan to create multiple PHP projects that use Telesign, we recommend that you group them within a `telesign_integrations` directory.
```
    cd ~/code/local
    mkdir telesign_integrations
    cd telesign_integrations
    mkdir {your project name}
    cd {your project name}
```

2. In the top-level directory for your project, create a new Composer project. 

```
composer init
```

Note that this command may need to be adjusted if your composer.phar file is not accessible in your PATH with the "composer" alias. If that is the case, reference the location of composer.phar on your file system for all Composer commands.

```
php {path to file}/composer.phar init
```

3. Enter the following selections when prompted:
  * **Package name (<vendor>/<name>) [{default vendor name}/{default package name}]:** `{your preferred vendor name}/{your project name}` Use the same project name you chose for the top-level directory in step 1 above.
  * **Description []:** Enter your preferred description or use the default.
  * **Author [{default author name and email address}, n to skip]:** Enter your preferred description, use the default, or skip.
  * **Minimum Stability []:** Enter your preferred value here or skip.
  * **Package Type (e.g. library, project, metapackage, composer-plugin) []:** Enter your preferred package type.
  * **License []:** Enter your preferred value here or skip.
  * **Would you like to define your dependencies (require) interactively [yes]?** Enter your preferred value here or use the default.
  * **Would you like to define your dev dependencies (require-dev) interactively [yes]?** Enter your preferred value here or use the default.
  * **Would you like to define your dev dependencies (require-dev) interactively [yes]?** Enter your preferred value here or use the default.
  * **Add PSR-4 autoload mapping? Maps namespace "{vendor}\{project}" to the entered relative path. [src/, n to skip]:** Enter your preferred value here or use the default.


3. Install the Telesign Self-service PHP SDK as a dependency in the top-level directory of your Composer project using this command. Once the SDK is installed, you should see a message in the terminal notifying you that you have successfully installed the SDK.

    `composer require telesign/telesign`

## Authentication

If you use a Telesign SDK to make your request, authentication is handled behind-the-scenes for you. All you need to provide is your Customer ID and API Key. The SDKs apply Digest authentication whenever they make a request to a Telesign service where it is supported. When Digest authentication is not supported, the SDKs apply Basic authentication.

## What's next

* Learn to send a request to Telesign with code with one of our [tutorials](https://developer.telesign.com/enterprise/docs/tutorials).  
* Browse our [Developer Portal](https://developer.telesign.com) for tutorials, how-to guides, reference content, and more.
* Check out our [sample code](https://github.com/TeleSign/sample_code) on GitHub.
