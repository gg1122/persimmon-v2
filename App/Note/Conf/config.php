<?php
return array(
    'URL_MODEL' => 2, //URL模式
    'URL_ROUTER_ON' => true,
    'URL_ROUTE_RULES' => array(
        //普通静态路由,
        'weibo/login' => 'Login/index',
        'weibo/auth' => 'Login/auth',
        'logout' => 'Login/logout',
        'post' => 'Index/post',
    )
);