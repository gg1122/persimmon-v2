<?php
namespace Note\Controller;

use Think\Controller;

/**
 * 首页控制器
 * Class IndexController
 * @package Home\Controller
 */
class IndexController extends controller
{
    /**
     *初始化，定义主题目录
     */
    public function _initialize()
    {
        //定义主题目录
        $theme = C('THEME') ? C('THEME') : C('DEFAULT_THEME');
        $this->_theme = TMPL_PATH . MODULE_NAME . '/' . $theme;
        $this->assign('_static', '/Public/static');
    }

    /**
     * 如果方法不存在，则执行这个方法
     * @param $name
     */
    public function _empty($name)
    {
        $_data = array(
            'success' => 10004,
            'info' => $name . 'Access deny'
        );
        $this->ajaxReturn($_data, 'json');
    }

    /**
     *首页
     */
    public function index()
    {
        $this->mate_title = "Psersimmon Note+";
        $this->display();
    }

    public function post()
    {
        //实例化Redis
        $expire = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : '3600';
        $this->Redis = new \Redis();
        $this->Redis->connect(C('REDIS_HOST'), C('REDIS_PORT'),$expire);
        //判断权限
        $ticketByCookie = cookie('ticket');
        $ticket = json_decode($ticketByCookie);
        $userInfo = $this->Redis->get($ticket);
        $this->user = unserialize($userInfo);

        $callback = str_replace('.net//','.net/',get_site_url().$_SERVER['REQUEST_URI']);
        $this->callback = base64_encode($callback);
        $this->assign('ticket',$ticket);
        $this->display();
    }
}