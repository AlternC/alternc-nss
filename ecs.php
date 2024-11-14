<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    //Include source project
    ->withPaths([
        __DIR__ . '/src',
    ])

    //Exclude debian directory as building content and no php code
    //Exclude composer content as imported from external ressources
    ->withSkip([
        __DIR__ . '/debian',
        __DIR__ . '/vendor',

    ])

    //Scan all root directory to check ecs itself
    ->withRootFiles()

    //Chek PER Coding Style 2.0
    //Replace old PSR-1 and PSR-12 rules
    ->withPhpCsFixerSets(perCS20: true, doctrineAnnotation: true)
;
