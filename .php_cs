<?php

return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->setRules(array(
        '@PSR2' => true,
        'align_multiline_comment' => true,
        'array_syntax' => true,
        'binary_operator_spaces' => true,
        'concat_space' => true,
        'dir_constant' => true,
        'include' => true,
        'is_null' => true,
        'method_separation' => true,
        'modernize_types_casting' => true,
        'native_function_casing' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_consecutive_blank_lines' => true,
        'no_leading_import_slash' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_short_bool_cast' => true,
        'no_short_echo_tag' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_whitespace_in_blank_line' => true,
        'not_operator_with_successor_space' => true,
        'non_printable_character' => array('use_escape_sequences_in_strings' => true),
        'normalize_index_brace' => true,
        'object_operator_without_whitespace' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_var_without_name' => true,
        'self_accessor' => true,
        'short_scalar_cast' => true,
        'single_quote' => true,
        'space_after_semicolon' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline_array' => true,
        'trim_array_spaces' => true,
        'visibility_required' => true,
        'whitespace_after_comma_in_array' => true,
    ))
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in('app')
            ->exclude('cache')
            ->exclude('config')
            ->exclude('logs')
            ->exclude('migrations')
            ->exclude('models/generated')
            ->exclude('sessions')
            ->exclude('sql')
    );
