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
    'SECURE_KEY' => '@persimmon_~KomQsggd,|c%-FjVB)F@l1.2gsdgsxpt.?6(dfgsds_zNHbQI>CHgfgsdg+]W',

    /* 模块绑定子域名 */
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
    //'SESSION_OPTIONS'=>array('domain'=>'.cong5.net'),//SESSION_OPTIONS的作用域，这个没使用到，无所谓

    /* 缓存配置 */
    'CACHE_TYPE'=>'redis',


    /* 数据缓存设置 */
    'DB_SQL_BUILD_CACHE' => true,
    'DATA_CACHE_PREFIX' => 'persimmon_', // 缓存前缀
    //'DATA_CACHE_TYPE' => 'Memcache', // 数据缓存类型
    'DATA_CACHE_TYPE' => 'Memcache',    //修改缓存缓存类型为Memcache
    'MEMCACHE_HOST' => 'tcp://127.0.0.1:6379', //memcache服务器地址和端口
    'DATA_CACHE_TIME' => '3600',  //过期的秒数

    /* 新浪微博接口 */
    'WB_AKEY'=>'************',//新浪开发平台的WB_AKEY,自行替换
    'WB_SKEY'=>'************',//新浪开发平台的WB_SKEY,自行替换
    'WB_CALLBACK_URL'=>'https://i.cong5.net/weibo/auth', //自行替换

    /* QQ互联接口 */
    'QQ_APP_ID' => '',
    'QQ_APP_KEY' => '',

    /* Wechat API */
    'WECHAT_TOKEN' => '',
    'WECHAT_APPID' => '',
    'WECHAT_APPSECRET' => '',

    /* 组件 */
    'PHANTOMJS' => '/Users/MrCong/PHP/phantomjs/phantomjs ./Public/fav/js/rasterize.js "%s" %s 800px*600px', //根据URL，调用本地软件生成快照图片.服务器上需要安装nodejs和phantomjs,路径自行替换 

    /* 工具API接口 */
    'PAGE2IMAGE_API' => 'http://api.page2images.com/restfullink?p2i_url=%s&p2i_device=6&p2i_screen=1024x768&p2i_size=300x300&p2i_imageformat=jpg&p2i_wait=5&p2i_key=<key>', //key自行替换 
    'WEATHER_API' => 'http://api.map.baidu.com/telematics/v3/weather?ak=<key>&output=json&location=', //天气查询接口，key自行替换 
    'IPADDR_API' => 'http://ip.taobao.com/service/getIpInfo.php?ip=%s', //IP查询接口，key自行替换 
    'TRANSLATE_API' => 'http://openapi.baidu.com/public/2.0/bmt/translate?client_id=<key>&q=%s&from=%s&to=%s', //百度翻译接口，key自行替换 
);