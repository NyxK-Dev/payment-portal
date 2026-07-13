<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';


define(
    'BASEPATH',
    dirname(__DIR__) . '/system/'
);


define(
    'APPPATH',
    dirname(__DIR__) . '/application/'
);


function &get_instance()
{
    global $CI;

    return $CI;
}


require_once APPPATH . 'interfaces/RoleRepositoryInterface.php';

require_once APPPATH . 'services/RoleService.php';