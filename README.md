# fadada SDK for PHP

[![Latest Stable Version](https://poser.pugx.org/yiui/fadada/v/stable)](https://packagist.org/packages/yiui/fadada)
[![Build Status](https://travis-ci.org/yiui/fadada.svg?branch=master)](https://travis-ci.org/yiui/fadada)
[![Coverage Status](https://coveralls.io/repos/github/yiui/fadada/badge.svg?branch=master)](https://coveralls.io/github/yiui/fadada?branch=master)

## [README of Chinese](https://github.com/yiui/fadada/blob/master/README-CN.md)

## Overview

fadada for php
## Run environment
- PHP 5.3+.
- cURL extension.

Tips:

- In Ubuntu, you can use the ***apt-get*** package manager to install the *PHP cURL extension*: `sudo apt-get install php5-curl`.

## Install OSS PHP SDK

- If you use the ***composer*** to manage project dependencies, run the following command in your project's root directory:

        composer require yiui/fadada

   You can also declare the dependency on fadada for PHP in the `composer.json` file.

        "require": {
            "yiui/fadada": "~1.0"
        }

   Then run `composer install` to install the dependency. After the Composer Dependency Manager is installed, import the dependency in your PHP code: 

        require_once __DIR__ . '/vendor/autoload.php';

- You can also directly download the packaged [PHAR File][releases-page], and 
   introduce the file to your code: 



## Quick use

### Common classes

### Initialize an OSSClient


```php
/**
*继承配置参数
***/
```

## License

- MIT

## Contact us

[releases-page]: https://github.com/yiui/fadada/releases
[phar-composer]: https://github.com/clue/phar-composer

