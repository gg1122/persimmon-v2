<?php

/**
 * Gavatar 头像缓存
 * @param            $email   [邮箱]
 * @param string     $size    [头像大小]
 * @param string     $default [默认图片]
 * @param bool|false $alt     [alt属性]
 * @return string
 * @author Willin Kan
 */
function my_avatar($email, $size = '50', $default = '', $alt = false)
{
    $alt = (false === $alt) ? '' : $alt;
    $f = md5(strtolower($email));
    $w = '/Public';
    $a = $w . '/avatar/' . $f . '.jpg';
    $t = 604800; //设定7天, 单位:秒
    if (empty($default)) $default = $w . '/avatar/default.jpg';
    if (!is_file($a) || (time() - filemtime($a)) > $t) { //当头像不存在或者文件超过7天才更新
        $g = sprintf("http://cn.gravatar.com", (hexdec($f{0}) % 2)) . '/avatar/' . $f . '?s=' . $size . '&d=' . $default;
        copy($g, $a);
    }
    if (filesize($a) < 500) copy($default, $a);
    $avatar = "<img title='{$alt}' alt='{$alt}' src='{$a}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
    return $avatar;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str     需要转换的字符串
 * @param string $start   开始位置
 * @param string $length  截取长度
 * @param string $charset 编码格式
 * @param string $suffix  截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    //$str = strip_tags($str);
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * Cookie 设置、获取、删除
 * @param string $name    cookie名称
 * @param mixed  $value   cookie值
 * @param mixed  $options cookie参数
 * @return mixed
 */
function notPreCookie($name, $value = '', $option = null)
{
    // 默认设置
    $config = array(
        'prefix' => C('COOKIE_PREFIX'), // cookie 名称前缀
        'expire' => C('COOKIE_EXPIRE'), // cookie 保存时间
        'path' => C('COOKIE_PATH'), // cookie 保存路径
        'domain' => C('COOKIE_DOMAIN'), // cookie 有效域名
    );
    // 参数设置(会覆盖黙认设置)
    if (!is_null($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config = array_merge($config, array_change_key_case($option));
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return null;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return null;
    }
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        if (isset($_COOKIE[$name])) {
            $value = $_COOKIE[$name];
            return json_decode(MAGIC_QUOTES_GPC ? stripslashes($value) : $value, true);
        } else {
            return null;
        }
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            // 设置cookie
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain']);
            $_COOKIE[$name] = $value;
        }
    }
    return null;
}

/**
 * 参数
 * 1：访问的URL
 * 2：post数据(不填则为GET)
 * 3：提交的$cookies
 * 4：是否返回$cookies
 * @param string $url
 * @param string $post
 * @param string $cookie
 * @param int    $returnCookie
 * @return mixed|string
 */
function curl_request($url, $post = '', $cookie = '', $returnCookie = 0)
{
    //检查是否开启CURL
    if (!function_exists('curl_init')) {
        return "CURL没有开启";
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/8.0 Mobile/11A465 Safari/9537.53");
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);

    if ($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if ($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    if ($returnCookie) {
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
        $info['cookie'] = substr($matches[1][0], 1);
        $info['content'] = $body;
        return $info;
    } else {
        return $data;
    }
}


/**
 * 获取HTTP || HTTPS类型
 * @return string
 * @author Mr.Cong <i@cong5.net>
 */
function http_type()
{
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    return $http_type;
}

/**
 * 获取网址
 * @return string
 * @author Mr.Cong <i@cong5.net>
 */
function get_site_url()
{
    return http_type() . $_SERVER['HTTP_HOST'] . '/';
}


/**
 * XSS过滤函数
 * @param $val [内容]
 * @return string
 * @author Don't konw.
 */
function remove_xss($val)
{
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}


/**
 * 把HTML转换成文本
 * @param $str
 * @return mixed|string
 * @author Mr.Cong <i@cong5.net>
 */
function html2Text($str)
{
    $str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU","",$str);
    $alltext = "";
    $start = 1;
    for($i=0;$i<strlen($str);$i++)
    {
        if($start==0 && $str[$i]==">")
        {
            $start = 1;
        }
        else if($start==1)
        {
            if($str[$i]=="<")
            {
                $start = 0;
                $alltext .= " ";
            }
            else if(ord($str[$i])>31)
            {
                $alltext .= $str[$i];
            }
        }
    }
    $alltext = str_replace("　"," ",$alltext);
    $alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
    $alltext = preg_replace("/[ ]+/s"," ",$alltext);
    return $alltext;
}

/**
 * 模型里面的自动完成
 * @return mixed
 * @author Mr.Cong <i@cong5.net>
 */
function get_uid()
{
    $uid = session('uid');
    return $uid;
}

/**
 * 获取IP地址所在的城市
 * @return string
 * @author Mr.Cong <i@cong5.net>
 */
function get_city_form_ip()
{
    $url = sprintf(C('IPADDR_API'),get_client_ip());
    $formCityWithIP = curl_request($url);
    $decodeData =  json_decode($formCityWithIP);
    return $decodeData->data->region.' '.$decodeData->data->city;
}

/**
 * 保存日志
 * @param $data
 * @author Mr.Cong <i@cong5.net>
 */
function write_log($data)
{
    $source = var_export($data,true);
    $path = './Public/log/'.date('Y').'/';
    if(!is_dir($path)){
        mkdir($path);
    }
    @file_put_contents($path.date('Ym').'.log',$source);
}

function getAddressByIp($ip){
    //获取IP地址
    $IpLocation = new \Org\Net\IpLocation();
    $ipAddress = $IpLocation->getlocation($ip);
    $address = $ipAddress["country"] . '-' . $ipAddress["area"] . ' (' . $ipAddress["ip"] . ')';
    return $address;
}