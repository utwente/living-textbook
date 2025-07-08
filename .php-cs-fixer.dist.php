<?php

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setIndent('  ')
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony'               => true,
        '@Symfony:risky'         => true,
        'array_indentation'      => true,
        'binary_operator_spaces' => [
            'default'   => 'align',
            'operators' => [
                '=>'  => 'align_single_space_minimal',
                '|'   => 'no_space',
                '+'   => 'single_space',
                '-'   => 'single_space',
                '*'   => 'single_space',
                '/'   => 'single_space',
                '??'  => 'single_space',
                '||'  => 'single_space',
                '&&'  => 'single_space',
                '===' => 'single_space',
                '=='  => 'single_space',
                '!==' => 'single_space',
                '!='  => 'single_space',
                '<'   => 'single_space',
                '<='  => 'single_space',
                '>'   => 'single_space',
                '>='  => 'single_space',
            ],
        ],
        'cast_spaces'                                      => ['space' => 'none'],
        'class_attributes_separation'                      => ['elements' => ['const' => 'only_if_meta']],
        'concat_space'                                     => ['spacing' => 'one'],
        'global_namespace_import'                          => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'increment_style'                                  => false,
        'method_chaining_indentation'                      => true,
        'native_constant_invocation'                       => true,
        'native_function_invocation'                       => ['include' => ['@all']],
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_imports'                                  => ['imports_order' => ['class', 'function', 'const']],
        'phpdoc_line_span'                                 => ['const' => 'single', 'method' => 'single', 'property' => 'single'],
        'phpdoc_order'                                     => true,
        'phpdoc_to_comment'                                => ['ignored_tags' => ['noinspection', 'noRector', 'var']],
        'single_line_throw'                                => false,
        'single_line_comment_spacing'                      => false,
        'yoda_style'                                       => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'migrations')
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'src')
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'phpunit')
    );
