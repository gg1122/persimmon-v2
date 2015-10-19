<?php

namespace Common\Model;
use Think\Model;

/**
 * User模型类
 * Class UserModel
 * @author Mr.Cong <i@cong5.net>
 */
class UsersModel extends Model
{
    protected $tableName = 'Users';

    protected $pk = 'uid';
    /**
     * 自动验证
     * @_validate array
     */
    protected $_validate = array(
        array('username','require','用户名不能为空'),
        array('password','require','密码不能为空')
    );

    /**
     * 字段
     * @var string
     */
    protected $field = "uid,username,password,salt,weibo_avatar,weibo_id,ticket";

    /**
     * 登陆方法
     * @param $username [用户名]
     * @param $password [密码]
     * @return array|bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function login($username = '',$password = '')
    {
        $map = array(
            'username' => array('eq',$username),
            'status' => array('neq', 1)
        );

        $userInfo = $this->_get($map, $this->field);
        //如果没有找到用户
        if (empty($userInfo)) {
            $this->error = 50004;
            return false;
        }

        if($password!=false){
            //如果密码不正确
            if ($userInfo['password'] != md5(md5($password.C('SECURE_KEY')).$userInfo['salt']) ) {
                $this->error = 50006;
                return false;
            }
        }

        return $userInfo;
    }

    /**
     * 获取指定字段的用户信息
     * @param $uid      [用户ID]
     * @param $field    [要获取的字段]
     * @return mixed
     * @author Mr.Cong <i@cong5.net>
     */
    public function get_user_field($uid,$field)
    {
        $field = empty($field) ? 'username' : $field;

        $map = array(
            $this->pk => array('eq',$uid)
        );

        $info = $this->where($map)->getField($field);

        return $info;
    }

    /**
     * 获取单条用户信息
     * @param array  $map   [查询条件]
     * @param string $field [查询字段]
     * @param string $order [排序条件]
     * @return array
     * @author Mr.Cong <i@cong5.net>
     */
    public function _get($map = array(), $field = '', $order = '')
    {
        $order = empty($order) ? $this->pk.' DESC' : $order;

        $info = $this->where($map)->field($field)->order($order)->find();

        $info = empty($info) ? array() : $info;

        return $info;
    }

    /**
     * 根据新浪微博返回的信息创建用户
     * @param $weibo_id         [微博ID]
     * @param $weibo_name       [微博名称]
     * @param $weibo_description[描述]
     * @param $weibo_address    [所在城市]
     * @param $weibo_avatar     [微博头像]
     * @param $weibo_domain     [微博域名]
     * @return bool|int
     * @author Mr.Cong <i@cong5.net>
     */
    public function createUserByWeibo(
        $weibo_id = '',
        $weibo_name = '',
        $weibo_description = '',
        $weibo_address = '',
        $weibo_avatar = '',
        $weibo_domain = ''
    ) {
        $nowTime = time();

        $shortStr = short($nowTime+rand(10));

        $userData = array(
            'username' => $weibo_name,
            'password' => md5(md5($weibo_id) . $nowTime),
            'description' => $weibo_description,
            'address' => $weibo_address,
            'wechat_openid' => '',
            'salt' => $shortStr[0],
            'ticket' => md5($shortStr[0]+rand(10)),
            'weibo_id' => $weibo_id,
            'weibo_domain' => $weibo_domain,
            'weibo_avatar' => $weibo_avatar,
            'create_time' => $nowTime,
            'status' => 0
        );

        $addPK = $this->add($userData);

        if ($addPK != false) {
            return $addPK;
        } else {
            $this->error = 50005;
            return false;
        }
    }

}