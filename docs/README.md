# Antidot Framework DI Container

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Dependency injection library built for Antidot Framework respecting PSR-11 standard

Can be used together with Antidot Framework or as a standalone piece for your application or customized framework

## Install

Via Composer

```bash
$ composer require antidot-fw/container:dev-master
```

## Simple Usage

```php
<?php

declare(strict_types=1);

use Antidot\Container\Builder;

$container = Builder::build([
    'services' => [
        'some.service' => Some::class,
    ],
], true);
if ($container->has('some.service')) {
    $service = $container->get('some.service');
}
```

## Config

It uses [Laminas Service Manager config](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/) alike pattern, 
and it allows some different options like dependencies with conditional parameters.

It can be configured with two different types, services and factories, services tels to the container how to instantiate a new reference to a class or identifier.  

```php
<?php

$config = [
    'services' => [
        // Service ID => Object Class,
        SomeSimpleObjectInterface::class => SomeSimpleObject::class,
        'some.simple.object' => AnotherSimpleObject::class,
        SomeComplexObjectInterfac::class => [
            'class' => SomeComplexObject::class,
            'arguments' => [
                'foo' => 'some.simple.object'
            ],
        ],
        'some.complex.object' => [
            'class' => AnotherComplexObject::class,
            'arguments' => [
                'foo' => 'some.simple.object',
                'bar' => SomeSimpleObjectInterface::class,
            ],
        ],
    ],
];
```

Factories are functions or classes that returns completely configured instances of described service identifier or class. 
In each case we pass ContainerInterface as first argument to factory method, in case of the factory classes you can pass extra parameters.

```php
<?php

$config = [
    'factories' => [
        SomeOtherObjectInterface::class => function (\Psr\Container\ContainerInterface $container): SomeOtherObject {
            return new SomeOtherObject(
                $container->get('service.one'),
                $container->get('service.two')
            );
        },
        SomeClassInterface::class => SomeClassFactory::class,
        SomeOtherClassInterface::class => [SomeOtherClassFactory::class, 'argument one', 'argument two'],
    ],
];

class SomeClassFactory {
    public function __invoke(\Psr\Container\ContainerInterface $container): SomeClass 
    {
        return new SomeClass(
            $container->get('some.other.class')
        );
    }
}

class SomeOtherClassFactory {
    public function __invoke(\Psr\Container\ContainerInterface $container, string $argument1, string $argument2): SomeClass 
    {
        return new SomeClass(
            $container->get($argument1),
            $container->get($argument2)
        );
    }
}
```

## Autowiring

You can enable or disable service autowiring as second parameter in the builder class, when is enabled the container will 
try to construct all known services without need of any config than service nme and service class.

#### Autowiring Limitations:

Service autowiring is not magic tool it can only create objects knows from the config, it fills class parameters, but it has to know who class to instantiate.

##### Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

##### Testing

``` bash
$ composer test
```

##### Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

##### Security

If you discover any security related issues, please email kpicaza@example.com instead of using the issue tracker.

##### Credits

- [Koldo Picaza][link-author]
- [All Contributors][link-contributors]

##### License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/antidot-fw/container.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/scrutinizer/build/g/antidot-framework/container.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/antidot-framework/container.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/antidot-framework/container.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/antidot-fw/container.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/antidot-fw/container
[link-travis]: https://scrutinizer-ci.com/g/antidot-framework/container/
[link-scrutinizer]: https://scrutinizer-ci.com/g/antidot-framework/container/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/antidot-framework/container/badges/coverage.png?b=master
[link-downloads]: https://packagist.org/packages/antidot-fw/container
[link-author]: https://github.com/kpicaza
[link-contributors]: ../../contributors
