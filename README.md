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

use Antidot\Container\ContainerBuilder;

$container = ContainerBuilder::build([
    'config' => [],
    'parameters' => [],
    'dependencies' => [
        'invokables' => [
            'some.service' => Some::class,
        ]
    ],
], true);
if ($container->has('some.service')) {
    $service = $container->get('some.service');
}
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email kpicaza@example.com instead of using the issue tracker.

## Credits

- [Koldo Picaza][link-author]
- [All Contributors][link-contributors]

## License

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
