<?php

namespace Api\Controller;

/**
 * 用户控制器
 * Class UserController
 * @package Note\Controller
 * @author  Mr.Cong <i@cong5.net>
 */

class UserController extends CommonController
{
    /**
     * 用户信息首页
     */
    public function profile()
    {
        $this->mate_title = '用户信息';
        $model = D('Users');
        $user_cookie = $this->_cookie_info;
        $map['username'] = $user_cookie['username'];
        $user = $model->field('username,email,about,address,banner')->where($map)->find();
        $this->assign('_url', U('/update'));
        $this->assign('user', $user);
        $this->display();
    }

    /**
     * 更新用户信息
     */
    public function update()
    {
        $model = D('Users');
        $user_cookie = $this->_cookie_info;
        if (IS_POST) {
            if ($_POST['password'] != false) {
                if ($_POST['password'] != $_POST['repassword']) {
                    $res = array(
                        'success' => 10003
                    );
                    $this->ajaxReturn($res);
                }
                $password = I('password');
                $data['password'] = md5($password . C('SECURE_KEY'));
            }
            $_POST['city'] && $data['address'] = I('city');
            $_POST['about'] && $data['about'] = I('about', '', '');
            $map['username'] = $user_cookie['username'];
            $result = $model->where($map)->save($data);
            if ($result != false) {
                $res = array(
                    'success' => 10001
                );
                $this->ajaxReturn($res);
            } else {
                $res = array(
                    'success' => 10004
                );
                $this->ajaxReturn($res);
            }
        }
    }
}