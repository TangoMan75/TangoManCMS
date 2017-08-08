TangoMan Callback Twig Extension Bundle
=======================================

How to install
--------------

With composer 

```console
$ composer require tangoman/callback-bundle
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
        new TangoMan\CallbackBundle\CallbackBundle(),
    );
}
```

You don't have to add **TangoMan CallbackBundle** to the `service.yml` of your project.
**twig.extension.callback** service should load automatically.

How to use
----------

Inside your views:

```twig
    <a href="{{ path('app_foo_bar', { 'callback': app.request.uri|callback }) }}">
        Your Foo Bar link here
    </a>
```

Inside your action method:

```php
    public function foobarAction(Request $request)
    {
        ...
        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
        ...
    }
```

Note
====

If you find any bug please report here : [Issues](https://github.com/TangoMan75/CallbackBundle/issues/new)

License
=======

Copyrights (c) 2017 Matthias Morin

[![License][license-GPL]][license-url]
Distributed under the GPLv3.0 license.

If you like **TangoMan CallbackBundle** please star!
And follow me on GitHub: [TangoMan75](https://github.com/TangoMan75)
... And check my other cool projects.

[tangoman.free.fr](http://tangoman.free.fr)

[license-GPL]: https://img.shields.io/badge/Licence-GPLv3.0-green.svg
[license-MIT]: https://img.shields.io/badge/Licence-MIT-green.svg
[license-url]: LICENSE
