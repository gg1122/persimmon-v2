<?php
return array(
    /* 默认模块 */
    'DEFAULT_MODULE' => 'Note',
    'MODULE_ALLOW_LIST' => array('Note'),

    //MySQL
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'persimmon_v2',
    'DB_USER' => 'persimmon_v2',
    'DB_PWD' => 'YJftNUYEDyBRZVjW',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'persimmon_',
    'SECURE_KEY' => 'Persimmon@BlueAfS3H4~KomQ,|c%-FjVB)F@l1.2xpt.?6(s_zNHbQI>CH+]W$%,[Y%kN_|=|Z0=>',

    /* 模块绑定 */
    'APP_SUB_DOMAIN_DEPLOY' => 1,
    'APP_SUB_DOMAIN_RULES' => array(
        'i' => 'Note',
        'api' => 'Api',
    ),

    /* 模版设置 */
    'DEFAULT_THEME'=>'default',

    /* Cookie设置 */
    'COOKIE_DOMAIN' => '.cong5.net',
    'COOKIE_PATH' => '/', //保存路径
    'COOKIE_PREFIX' => 'persm_', //cookie的前缀
    'COOKIE_EXPIRE' => 3600, //cookie的生存时间

    /* SESSION设置 */
    'SESSION_TYPE' => 'Redis', //session保存类型
    'SESSION_PREFIX' => 'sess_', //session前缀
    'REDIS_HOST' => '127.0.0.1', //REDIS服务器地址
    'REDIS_PORT' => 6379, //REDIS连接端口号
    //'SESSION_EXPIRE' => 3600, //SESSION过期时间
    'SESSION_OPTIONS'=>array('domain'=>'.cong5.net'),//session配置

    /* 缓存配置 */
    'CACHE_TYPE'=>'redis',


    /* 数据缓存设置 */
    'DB_SQL_BUILD_CACHE' => true,
    'DATA_CACHE_PREFIX' => 'persimmon_', // 缓存前缀
    //'DATA_CACHE_TYPE' => 'File', // 数据缓存类型
    
    'DATA_CACHE_TYPE' => 'Memcached',    //修改缓存缓存类型为Memcache
    'MEMCACHE_HOST' => 'tcp://127.0.0.1:11211', //memcache服务器地址和端口
    'DATA_CACHE_TIME' => '3600',  //过期的秒数

    /* 新浪微博接口 */
    'WB_AKEY'=>'1627439536',
    'WB_SKEY'=>'00fed59784a91abc0c34019dd9b14b16',
    'WB_CALLBACK_URL'=>'https://i.cong5.net/weibo/auth',

    /* QQ互联接口 */
    'QQ_APP_ID' => '',
    'QQ_APP_KEY' => '',

    /* Wechat API */
    'WECHAT_TOKEN' => '',
    'WECHAT_APPID' => '',
    'WECHAT_APPSECRET' => '',

    /* 组件 */
    'PHANTOMJS' => '/Users/MrCong/PHP/phantomjs/phantomjs ./Public/fav/js/rasterize.js "%s" %s 800px*600px',

    /* 工具API接口 */
    'PAGE2IMAGE_API' => 'http://api.page2images.com/restfullink?p2i_url=%s&p2i_device=6&p2i_screen=1024x768&p2i_size=300x300&p2i_imageformat=jpg&p2i_wait=5&p2i_key=f0c472a21c0b7543',
    'WEATHER_API' => 'http://api.map.baidu.com/telematics/v3/weather?ak=9acf725168b515ff5dda7925017faa9b&output=json&location=',
    'IPADDR_API' => 'http://ip.taobao.com/service/getIpInfo.php?ip=%s',
    'TRANSLATE_API' => 'http://openapi.baidu.com/public/2.0/bmt/translate?client_id=GphpeOg9iVUhlAQL87incLMg&q=%s&from=%s&to=%s',
);