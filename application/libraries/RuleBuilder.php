<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class RuleBuilder
{


    public static function required(
        $field,
        $label
    )
    {
        return [
            'field'=>$field,
            'label'=>$label,
            'rules'=>'required'
        ];
    }



    public static function email()
    {
        return 'required|valid_email';
    }



    public static function string($max=null)
    {

        if($max)
        {
            return "required|max_length[$max]";
        }


        return "required";
    }



    public static function integer()
    {
        return "required|integer";
    }



    public static function decimal()
    {
        return "required|decimal";
    }



    public static function optional($rules)
    {
        return "permit_empty|".$rules;
    }

}