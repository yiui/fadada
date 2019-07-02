# fadada SDK for PHP



## Overview

fadada for php
## Run environment
- PHP 5.3+.
- cURL extension.

Tips:

- In Ubuntu, you can use the ***apt-get*** package manager to install the *PHP cURL extension*: `sudo apt-get install php5-curl`.

## 安装

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

### 安装


```php
/**
<?php

namespace common\components;

use yiui\fadada\FddServer;

/**
 *
 * 法大大接口继承,
 *
 **/
class Fadada extends FddServer
{
    public $appId;
    public $appSecret;
    public $host;

    public function __construct()
    {
        $this->appId = "";
        $this->appSecret = "";
        $this->host = '';
        parent::__construct($this->appId, $this->appSecret, $this->host);
    }
}
***/
```

## License

- MIT

## Contact us

[releases-page]: https://github.com/yiui/fadada/releases
[phar-composer]: https://github.com/clue/phar-composer

