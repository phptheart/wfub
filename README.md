# Generator userbars for Warface [![Latest Stable Version](https://poser.pugx.org/wnull/wfub/v)](//packagist.org/packages/wnull/wfub) [![Total Downloads](https://poser.pugx.org/wnull/wfub/downloads)](//packagist.org/packages/wnull/wfub) [![License](https://poser.pugx.org/wnull/wfub/license)](//packagist.org/packages/wnull/wfub)

Library for generating game userbars Warface on PHP.

## Prerequisites

| Name               | Version |
|  ---               |   ---   |
| php                | \>=7.4  |
| wnull/warface-api  |  ^2.1   |
| ext-imagick        |    *    |

## Installation

This generator can be installed using Composer by running the following command:

```sh
composer require wnull/wfub
```

## Example of use

Before using, you should read a documentation about the functions and their parameters. 

```php
// Plug-in dependencies through by Composer
require __DIR__ . '/vendor/autoload.php';

// Creating an instance of a class with a query
$client = new WFub\Draw('Сцена', Warface\Enums\GameServer::ALPHA);
// Generating an Imagick image object
$image = $client->create();
```

## License

This library is licensed under the [MIT License](https://github.com/wnull/wfub/blob/master/LICENSE).
