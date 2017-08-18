TangoMan Role Bundle
====================

**TangoMan Role Bundle** provides basis for user roles / privileges management.


How to install
--------------

With composer

```console
$ composer require tangoman/role-bundle
```


Enable the bundle
-----------------

Don't forget to enable the bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new TangoMan\RoleBundle\TangoManRoleBundle(),
    );
}
```


Create your Role entity
-----------------------

```php
<?php

namespace AppBundle\Entity;

use TangoMan\RoleBundle\Model\Role as TangoManRole;

/**
 * Class Role
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 * @ORM\Table(name="role")
 */
class Role extends TangoManRole
{
    // ...

    public function __construct()
    {
        parent::__construct();
        // ...
    }
}
```


Create your Privilege entity
----------------------------

```php
<?php

namespace AppBundle\Entity;

use TangoMan\RoleBundle\Model\Privilege as TangoManPrivilege;

/**
 * Class Privilege
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrivilegeRepository")
 * @ORM\Table(name="privilege")
 */
class Privilege extends TangoManPrivilege
{
    // ...

    public function __construct()
    {
        parent::__construct();
        // ...

    }
}
```


Inside your User entity
-----------------------

```php
<?php

namespace AppBundle\Entity;

use TangoMan\UserBundle\Model\User as TangoManUser;
use TangoMan\RoleBundle\Model\Privilege;
use TangoMan\RoleBundle\Model\Role;

/**
 * Class User
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends TangoManUser
{
    // ...

    private $roles;

    public function __construct()
    {
        parent::__construct();
        // ...
    }

    public function getRoles()
    {
        return $this->roles;
    }

}
```

Note
====

If you find any bug please report here : [Issues](https://github.com/TangoMan75/RepositoryHelper/issues/new)

License
=======

Copyrights (c) 2017 Matthias Morin

[![License][license-GPL]][license-url]
Distributed under the GPLv3.0 license.

If you like **TangoMan Role Bundle** please star!
And follow me on GitHub: [TangoMan75](https://github.com/TangoMan75)
... And check my other cool projects.

[tangoman.free.fr](http://tangoman.free.fr)

[license-GPL]: https://img.shields.io/badge/Licence-GPLv3.0-green.svg
[license-MIT]: https://img.shields.io/badge/Licence-MIT-green.svg
[license-url]: LICENSE
