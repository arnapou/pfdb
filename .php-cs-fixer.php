<?php

declare(strict_types=1);

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$header = <<<HEADER
    This file is part of the Arnapou PFDB package.

    (c) Arnaud Buathier <arnaud@arnapou.net>

    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.
    HEADER;

$excludes = [
    'database',
];

$dirs = [
    __DIR__ . '/demo',
    __DIR__ . '/src',
    __DIR__ . '/tests',
];

$rules = [
    '@PSR2' => true,
    '@PSR12' => true,
    '@Symfony' => true,
    '@DoctrineAnnotation' => true,
    '@PHP80Migration' => true,
    '@PHP81Migration' => true,
    '@PHP82Migration' => true,
    'declare_strict_types' => true,
    'concat_space' => ['spacing' => 'one'],
    'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['const', 'class', 'function']],
    'native_function_invocation' => ['include' => ['@compiler_optimized']],
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'phpdoc_order' => true,
    'phpdoc_var_annotation_correct_order' => true,
    'global_namespace_import' => ['import_classes' => true, 'import_functions' => false, 'import_constants' => false],
    'header_comment' => ['location' => 'after_declare_strict', 'header' => $header],
    'phpdoc_line_span' => ['const' => 'single', 'method' => 'multi', 'property' => 'single'],
    'phpdoc_to_comment' => false,
];

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder((new PhpCsFixer\Finder())->exclude($excludes)->in($dirs));
