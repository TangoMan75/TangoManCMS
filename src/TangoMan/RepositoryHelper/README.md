TangoMan Repository Helper
==========================

**TangoMan Repository Helper** adds useful functions to your repository.

How to install
--------------

With composer 

```console
$ composer require tangoman/callback-bundle
```

**TangoMan Repository Helper** is not a bundle, no need to enable nothing in the kernel.


How to use
----------

Inside your repository:
Add "use" statement just like when you're using a trait.

```php
<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class FoobarRepository
 *
 * @package AppBundle\Repository
 */
class FoobarRepository extends EntityRepository
{
    use RepositoryHelper;
}
```

Inside your controller:

```php
class FoobarController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated user list
        $em = $this->get('doctrine')->getManager();
        $foobars = $em->getRepository('AppBundle:Foobar')->findByQuery($request->query);

        return $this->render(
            'admin/foobar/index.html.twig',
            [
                'foobars' => $foobars,
            ]
        );
    }
```

Query Parameters
----------------

order : switch-entity-property
way   : "ASC", "DESC"
limit : integer
page  : integer
join  : switch-entity-property


Switches
--------

Set default values for (mode/action)-entity-property
a : mode andWhere
o : mode orWhere
r : mode orderBy
b : action boolean
e : action exact match
l : action like
n : action not null
s : action simple array
c : action orderBy count
p : action orderBy property (alphabetical)


Note
====

If you find any bug please report here : [Issues](https://github.com/TangoMan75/RepositoryHelper/issues/new)

License
=======

Copyrights (c) 2017 Matthias Morin

[![License][license-GPL]][license-url]
Distributed under the GPLv3.0 license.

If you like **TangoMan Repository Helper** please star!
And follow me on GitHub: [TangoMan75](https://github.com/TangoMan75)
... And check my other cool projects.

[tangoman.free.fr](http://tangoman.free.fr)

[license-GPL]: https://img.shields.io/badge/Licence-GPLv3.0-green.svg
[license-MIT]: https://img.shields.io/badge/Licence-MIT-green.svg
[license-url]: LICENSE
