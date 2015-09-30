<?php
/**
 * Created by PhpStorm.
 * User: Mr.Cong <i@cong5.net>
 * Date: 2015/8/31
 * Time: 10:31
 */

namespace Common\Model;


use Think\Model;

class LoginLogModel extends Model
{
    protected $tableName = 'login_logs';

    /**
     * 写登陆日志
     * @param string $username  [用户名]
     * @param string $method    [登陆方式]
     * @author Mr.Cong <i@cong5.net>
     */
    public function writeLog($username = '',$password = '', $method = 'weibo')
    {
        $loginLog = array(
            'login_ip' => get_client_ip(),
            'login_time' => time(),
            'username' => $username,
            'password' => $password,
            'method' => $method,
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        );
        $this->add($loginLog);
    }
}