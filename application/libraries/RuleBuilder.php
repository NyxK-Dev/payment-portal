<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RuleBuilder
{

    public static function make(
        $field,
        $label,
        $rules
    ) {
        return [
            'field' => $field,
            'label' => $label,
            'rules' => $rules
        ];
    }


    public static function required()
    {
        return 'required';
    }


    public static function optional($rules)
    {
        return 'permit_empty|' . $rules;
    }


    public static function email()
    {
        return 'required|valid_email';
    }
    public static function requiredArray()
    {
        return 'required';
    }


    public static function password($min = 8)
    {
        return "required|min_length[$min]";
    }


    public static function integer()
    {
        return 'integer';
    }


    public static function numeric()
    {
        return 'numeric';
    }


    public static function decimal()
    {
        return 'decimal';
    }


    public static function boolean()
    {
        return 'in_list[0,1]';
    }


    public static function string($max = null)
    {
        if ($max) {
            return "trim|max_length[$max]";
        }

        return 'trim';
    }


    public static function min($length)
    {
        return "min_length[$length]";
    }


    public static function max($length)
    {
        return "max_length[$length]";
    }


    public static function array()
    {
        return 'is_array';
    }


    public static function greaterThanEqual($value)
    {
        return "greater_than_equal_to[$value]";
    }


    public static function greaterThan($value)
    {
        return "greater_than[$value]";
    }


    public static function in(array $values)
    {
        return 'in_list[' . implode(',', $values) . ']';
    }


    public static function matches($field)
    {
        return "matches[$field]";
    }


    public static function alphaNumeric()
    {
        return 'alpha_numeric';
    }


    public static function unique($table, $field)
    {
        return "is_unique[$table.$field]";
    }


    public static function combine(...$rules)
    {
        return implode(
            '|',
            array_filter($rules)
        );
    }
}
