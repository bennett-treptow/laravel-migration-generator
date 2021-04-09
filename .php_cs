<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sortAlgorithm' => 'length'],
        'method_separation' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_after_namespace' => true,
        'binary_operator_spaces' => [
            'default' => 'single_space',
            'operators' => [
                '=' => 'single_space',
                '=>' => 'align_single_space_minimal',
            ],
        ],
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return'],
        ],
        'braces' => [
            'allow_single_line_closure' => false,
            'position_after_anonymous_constructs' => 'same',
            'position_after_control_structures' => 'same',
            'position_after_functions_and_oop_constructs' => 'next'
        ],
        'cast_spaces' => [
            'space' => 'single'
        ],
        'class_attributes_separation' => [
            'elements' => ['method', 'property'],
        ],
        'class_keyword_remove' => false,
        'combine_consecutive_unsets' => true,
        'combine_consecutive_issets' => true,
        'concat_space' => ['spacing' => 'one'],
        'constant_case' => ['case' => 'lower'],
        'declare_equal_normalize' => ['space' => 'single'],
        'full_opening_tag' => true,
        'function_declaration' => [
            'closure_function_spacing' => 'one',
        ],
        'function_typehint_space' => true,
        'single_line_comment_style' => [
            'comment_types' => ['asterisk', 'hash'],
        ],
        'lowercase_cast' => true,
        'lowercase_keywords' => true,
        'lowercase_static_reference' => true,
        'method_argument_space' => [
            'keep_multiple_spaces_after_comma' => false,
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'method_chaining_indentation' => true,
        'modernize_types_casting' => true,
        'multiline_comment_opening_closing' => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'native_function_casing' => true,
        'native_function_type_declaration_casing' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_closing_tag' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_extra_blank_lines' => [
            'tokens' => ['curly_brace_block', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'throw', 'use'],
        ],
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_short_echo_tag' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_trailing_whitespace' => true,
        'no_unused_imports' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'not_operator_with_successor_space' => true,
        'single_blank_line_before_namespace' => true,
        'single_line_after_imports' => true,
        'single_quote' => true,
        'single_trait_insert_per_statement' => true,
        'standardize_not_equals' => true,
        'whitespace_after_comma_in_array' => true,
        'include' => true,
        'no_spaces_around_offset' => [
            'positions' => ['inside', 'outside'],
        ],
        'object_operator_without_whitespace' => true,
        'phpdoc_single_line_var_spacing' => true,
        'ternary_operator_spaces' => true,
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
        'return_type_declaration' => [
            'space_before' => 'none',
        ],
    ))
    ->setRiskyAllowed(true)
    ->setLineEnding("\n")
    ->setFinder($finder);
