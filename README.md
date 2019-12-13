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

Method `edit()` allows you to edit only the data that is present on the final generated image.

  ```php
  $image = (new Draw(
      (new Client)
          ->get('Пиктография', Warface::ALPHA)
          ->edit(['rank_id' => 90])
  ))->init();
  ```

#### 4.3. Use with the addition of game achievements
 
Method `addAchievements()` includes only array with keys: `badges`, `marks`, `strips`.
 
   ```php
  $image = (new Draw(
      (new Client)
          ->get('Пиктография', Warface::ALPHA)
          ->edit(['rank_id' => 90, 'clan_name' => null])
          ->addAchievements(['marks' => 417])
  ))->init();
   ```
   
*View the entire [catalog of achievements](https://wfts.su/achievements) with IDs.*
    
 
After that, an image object (Imagick) is created, which can either be displayed or written to a file.

## 5. Result

Examples of generated images:

![English](https://user-images.githubusercontent.com/33278849/70607823-33a92900-1c10-11ea-9a84-7e0d97d210a5.png)
![Russian](https://user-images.githubusercontent.com/33278849/70812463-b5e44980-1dd8-11ea-9b8b-f67b4dd2e003.png)
## License

This library is licensed under the [MIT License](https://github.com/wnull/userbars-warface-generator/blob/master/LICENSE).
