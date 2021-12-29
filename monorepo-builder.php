<?php

declare(strict_types=1);

// File: monorepo-builder.php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // where are the packages located?
    $parameters->set(Option::PACKAGE_DIRECTORIES, [
        // default value
        __DIR__ . '/Modules',
    ]);

    // what extra parts to add after merge?
    $parameters->set(Option::DATA_TO_APPEND, [
        ComposerJsonSection::AUTOLOAD_DEV => [
            'psr-4' => [
                'Symplify\Tests\\' => 'tests',
            ],
        ],
        ComposerJsonSection::REQUIRE_DEV => [
            'phpstan/phpstan' => '^0.12',
        ],
    ]);

    $parameters->set(Option::DATA_TO_REMOVE, [
        ComposerJsonSection::REQUIRE => [
            // the line is removed by key, so version is irrelevant, thus *
            'phpunit/phpunit' => '*',
        ],
    ]);
};
