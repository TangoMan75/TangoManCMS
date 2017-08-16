TangoMan Repository Helper
==========================

**TangoMan Repository Helper** adds useful functions to your repositories.

How to install
--------------

With composer 

```console
$ composer require tangoman/repository-helper
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
 * @package FoobarBundle\Repository
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


Inside your views:

Search Form
```twig
<label for="inputUser">User</label>
<input type="text" name="user-username" id="inputUser" class="form-control"
    value="{{ app.request.get('user-username')|join(', ') }}"/>
```
Will generate this: 
.../admin/posts/?user-username=admin


Order Link
```twig
<th class="{{ app.request.query.get('order') == 'user-username' ? app.request.query.get('way', 'ASC') }}">
    <a href="{{ path('app_admin_post_index', app.request.query.all|merge({
        'page'  : 1,
        'order' : 'user-username',
        'way'   : app.request.query.get('order') == 'user-username'
        and app.request.query.get('way', 'ASC') == 'ASC' ? 'DESC' : 'ASC'})) }}">
        User
    </a>
</th>
```
Will generate this: 
.../admin/posts/?page=1&order=user-username&way=ASC


Query Parameters
----------------

 - order : string  : switch-entity-property
 - way   : string  : ASC/DESC
 - limit : integer : 1 -> ~
 - page  : integer : 1 -> ~
 - join  : string : switch-entity-property


Switches
--------

Switch values for mode/action
 - a : mode andWhere (search)
 - o : mode orWhere (search)
 - r : mode orderBy
 - b : action boolean
 - e : action exact match
 - l : action like
 - n : action not null
 - s : action simple array
 - c : action orderBy count
 - p : action orderBy property (alphabetical)


Helper Functions
----------------

count($criteria = [])
 - Returns element count

getTableName()
 - Returns current table name

findAllPaged($page = 1, $limit = 10, $criteria = [])
 - Returns result with pagination (no query support)

findByQuery(ParameterBag $query, $criteria = [])
 - Returns query result with pagination

findByQueryScalar(ParameterBag $query, $criteria = [])
 - Return query as scalar result (for export or API)

export(ParameterBag $query, $criteria = [])
 - Return all objects as scalar result (no query support)


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
