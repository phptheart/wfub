# Generator userbars Warface

Simple and free library to generate userbars game Warface.



## 1. Prerequisites

* PHP 7.1 or later
* Imagick

## 2. Installation

This generator can be installed using Composer by running the following command:

```sh
composer require wnull/userbars-warface-generator:dev-master
```

## 3. Initialization

Import the required classes:

```php
require __DIR__.'/vendor/autoload.php';

use WF\{Stamp\Draw, Exception\Trash, Client, Enums\Warface};
```

## 4. Example of use

Before using, you should read a documentation about the functions and their parameters. 

#### 4.1. Basic usage:

  ```php
  $image = (new Draw(
      (new Client)->get('JAWAR', Warface::US)
  ))->init();
  ```
#### 4.2. Use with changes in game statistics:

  ```php
  $image = (new Draw(
      (new Client)
          ->get('Пиктография', Warface::ALPHA)
          ->edit(['rank_id' => 90])
  ))->init();
  ```
  
After that, an image object (Imagick) is created, which can either be displayed or written to a file.

## 5. Result

Examples of generated images, without game achievements in png format:

![English](https://user-images.githubusercontent.com/33278849/70607823-33a92900-1c10-11ea-9a84-7e0d97d210a5.png)
![Russian](https://user-images.githubusercontent.com/33278849/70607827-3441bf80-1c10-11ea-991e-dc55818aaa8e.png)

## License

This library is licensed under the [MIT License](https://github.com/wnull/userbars-warface-generator/blob/master/LICENSE).
