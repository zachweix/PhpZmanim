# PhpZmanim
PHP port of Kosher Java

See Kosher Java documentation for how to use method names and comments for every method and variable. Any method name you see that starts with `get` (such as `getSunrise`) can also be called in the Zmanim class as `->get("Sunrise")`. This was done, so you can have an array of Zmanim names and loop through them. 

Eventually, I hope to add more documentation to this file.

## Installation

### With Composer

```
$ composer require zachweix/php-zmanim
```

```json
{
    "require": {
        "zachweix/php-zmanim": "^1.0"
    }
}
```

```php
<?php
require 'vendor/autoload.php';

use PhpZmanim\Zmanim;

$zmanimInGMT = Zmanim::create(2019, 2, 21, "Greenwich");
$zmanimInNYC = Zmanim::create(2019, 2, 21, "New York City", 40.850519, -73.929214, 200, "America/New_York");

$sunrise = $zmanimInGMT->getSunrise();
$sunrise = $zmanimInNYC->get("Sunrise");
```

<a name="install-nocomposer"/>

### Without Composer

Download the PhpZmanim [latest release](https://github.com/zachweix/PhpZmanim/releases) and put the contents of the ZIP archive into a directory in your project. Then require the file `autoload.php` to get all classes and dependencies loaded on need. You will need to make sure to include Nesbot Carbon as well.

```php
<?php
require 'path-to-PhpZmanim-directory/autoload.php';

use PhpZmanim\Zmanim;

$zmanimInGMT = Zmanim::create(2019, 2, 21, "Greenwich");
$zmanimInNYC = Zmanim::create(2019, 2, 21, "New York City", 40.850519, -73.929214, 200, "America/New_York");

$sunrise = $zmanimInGMT->getSunrise();
$sunrise = $zmanimInNYC->get("Sunrise");
```
