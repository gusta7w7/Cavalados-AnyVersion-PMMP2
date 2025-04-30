<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PSR12' => true,
    '@PHP71Migration' => true,
    'declare_strict_types' => false,
    'linebreak_after_opening_tag' => false,
    'blank_line_after_opening_tag' => false,
    'php_unit_internal_class' => false,
    'php_unit_test_class_requires_covers' => false,
    'php_unit_size_class' => false,
    'logical_operators' => true,
    'concat_space' => [
        'spacing' => 'one'
    ],
    'array_syntax' => [
        'syntax' => 'short'
    ],
    'list_syntax' => [
        'syntax' => 'long'
    ],
    'binary_operator_spaces' => [
        'operators' => ['|' => null]
    ],
    'visibility_required' => [
        'elements' => ['property', 'method']
    ],
    'global_namespace_import' => [
        'import_constants' => true,
        'import_functions' => true,
        'import_classes' => null,
    ],
    'native_constant_invocation' => [
        'scope' => 'namespaced'
    ],
    'native_function_invocation' => [
        'scope' => 'namespaced',
        'include' => ['@all'],
    ],
    'no_unused_imports' => true,
    'ordered_imports' => [
        'imports_order' => [
            'class',
            'function',
            'const',
        ],
        'sort_algorithm' => 'alpha'
    ],
    'class_attributes_separation' => [
        'elements' => [
            'method' => 'one',
        ],
   ],
    'switch_case_space' => true,
    'return_type_declaration' => ['space_before' => 'one'],
    'no_trailing_whitespace' => true,
    'normalize_index_brace' => true,
    'binary_operator_spaces' => ['operators' => ['=>' => 'align_single_space_minimal']],
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'indentation_type' => true,
    'array_indentation' => true,
    'whitespace_after_comma_in_array' => true,
];

return (new Config)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);