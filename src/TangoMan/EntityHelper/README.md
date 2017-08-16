TangoMan Entity Helper
==========================

**TangoMan Entity Helper** provides Getters and Setters for common properties.


How to install
--------------

With composer 

```console
$ composer require tangoman/entity-helper
```

**TangoMan Entity Helper** is not a bundle, no need to enable nothing in the kernel.


How to use
----------

Inside your entity:
Add "use" statement just like when you're using a trait.

```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityEntity;
use TangoMan\EntityHelper\Categorized;
use TangoMan\EntityHelper\Embeddable;
use TangoMan\EntityHelper\Featurable;
use TangoMan\EntityHelper\HasClickDate;
use TangoMan\EntityHelper\HasIcon;
use TangoMan\EntityHelper\HasLabel;
use TangoMan\EntityHelper\HasName;
use TangoMan\EntityHelper\HasSummary;
use TangoMan\EntityHelper\HasText;
use TangoMan\EntityHelper\HasTitle;
use TangoMan\EntityHelper\HasType;
use TangoMan\EntityHelper\HasViews;
use TangoMan\EntityHelper\Illustrable;
use TangoMan\EntityHelper\Privatable;
use TangoMan\EntityHelper\Publishable;
use TangoMan\EntityHelper\Sluggable;
use TangoMan\EntityHelper\Slugify;
use TangoMan\EntityHelper\Timestampable;
use TangoMan\EntityHelper\UploadableDocument;
use TangoMan\EntityHelper\UploadableImage;

/**
 * Class Foobar
 *
 * @package AppBundle\Entity
 */
class Foobar
{
    use Categorized;
    use Embeddable;
    use Featurable;
    use HasClickDate;
    use HasIcon;
    use HasLabel;
    use HasName;
    use HasSummary;
    use HasText;
    use HasTitle;
    use HasType;
    use HasViews;
    use Illustrable;
    use Privatable;
    use Publishable;
    use Sluggable;
    use Slugify;
    use Timestampable;
    use UploadableDocument;
    use UploadableImage;
}
```

Don't forget to update database schema

```console
$ php bin/console schema:update
```


Note
====

If you find any bug please report here : [Issues](https://github.com/TangoMan75/EntityHelper/issues/new)

License
=======

Copyrights (c) 2017 Matthias Morin

[![License][license-GPL]][license-url]
Distributed under the GPLv3.0 license.

If you like **TangoMan Entity Helper** please star!
And follow me on GitHub: [TangoMan75](https://github.com/TangoMan75)
... And check my other cool projects.

[tangoman.free.fr](http://tangoman.free.fr)

[license-GPL]: https://img.shields.io/badge/Licence-GPLv3.0-green.svg
[license-MIT]: https://img.shields.io/badge/Licence-MIT-green.svg
[license-url]: LICENSE
