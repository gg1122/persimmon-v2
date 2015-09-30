<?php
namespace Fav\Controller;

use Think\Controller;

/**
 * 收藏控制器
 * Class IndexController
 * @package Fav\Controller
 */
class IndexController extends Controller
{
    public function _initialize()
    {
        $this->_static = '/Public/Fav';
    }
    /**
     * 首页
     * @author Mr.Cong <i@cong5.net>
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 快速收藏
     * @author Mr.Cong <i@cong5.net>
     */
    public function post()
    {
        $this->user = cookie('fav');
        $this->display();
    }

    public function login()
    {

        $post = I('post.');
        //如果用户名或者密码为空
        if ($post['username'] == '' || $post['password'] == '') {
            $data = array(
                'status' => 0,
                'info' => '用户名或者密码不能为空'
            );
            $this->ajaxReturn($data, 'json');
            return false;
        }
        //验证登陆
        $userModel = D('Common/Users');
        $userRes = $userModel->login($post['username'], $post['password']);

        if ($userRes == false) {
            $error = $userModel->getError();
            switch ($error) {
                case 50004:
                    $error_msg = '用户不存在';
                    break;
                case 50006:
                    $error_msg = '用户或者密码错误';
                    break;
                default:
                    break;
            }
            $data = array(
                'status' => 0,
                'info' => $error_msg
            );

            $this->ajaxReturn($data, 'json');
            exit();
        }
        //生成cookie
        $cookie_array = array(
            'uid' => $userRes['uid'],
            'username' => $userRes['username'],
            'weibo_avatar' => $userRes['weibo_avatar']
        );

        cookie('fav',$cookie_array,3600);

        $data = array(
            'status' => 1,
            'info' => '登陆成功'
        );

        $this->ajaxReturn($data, 'json');

    }

}