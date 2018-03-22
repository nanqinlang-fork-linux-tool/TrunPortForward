<?php
return array(
    'LOAD_EXT_CONFIG'         => 'dbc',
    'URL_ROUTER_ON'   => true,
    'URL_MAP_RULES' => array(
        'auth/login' => 'Auth/login',
        'vms' => 'Vm/vmList',
        'vm/add' => 'Vm/addVm',

        //Port
        'port/applies' => 'Port/applies_view',
        'port/apply' => 'Port/apply',


        //operator
        'operator/managePorts' => 'Operator/managePorts',
        'operator/applies' => 'Operator/applies',
        'operator/siteConfig' => 'Operator/siteConfig',
    ),
    'URL_ROUTE_RULES' => array(
    ),
);