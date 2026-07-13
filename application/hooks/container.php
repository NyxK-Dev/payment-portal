<?php

require_once APPPATH . 'interfaces/RoleRepositoryInterface.php';

require_once APPPATH . 'repositories/RoleRepository.php';

require_once APPPATH . 'core/ServiceContainer.php';


$container = new ServiceContainer();


$container->bind(
    RoleRepositoryInterface::class,
    RoleRepository::class
);


$GLOBALS['container'] = $container;