<?php
/**
 * Created by PhpStorm.
 * User: MrCong
 * Date: 15/4/29
 * Time: 下午9:58
 */
namespace Api\Controller;


use Think\Controller\RestController;

class CommonController extends RestController
{
    protected $ticket;
    protected $userInfo = array();
    protected $Redis;
    /**
     *初始化，定义主题目录
     */
    public function _initialize()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Origin,Accept, Content-Type, X-Requested-With, X-CSRF-Token');
        //实例化Redis
        $expire = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : '3600';
        $this->Redis = new \Redis();
        $this->Redis->connect(C('REDIS_HOST'), C('REDIS_PORT'),$expire);
        //判断权限
        $this->ticket = I('auth','');
        $get_user_info = $this->Redis->get($this->ticket);
        $this->userInfo = unserialize($get_user_info);
        //如果Redis里面没有用户数据，则清空cookie，返回无权限的提示
        if($this->userInfo['uid']==false){
            cookie('ticket',null);
            $_data = array(
                'success' => 50003,
                'info' => 'Access deny'
            );
            $this->response($_data, 'json');
        }

    }

    /**
     * 返回结果提示信息
     * [用途：尽量返回简单的提示内容给用户,不要抛出其他多余的提示信息,不要让用户去思考]
     * @param string $result  [操作结果]
     * @param string $success [成功的提示文字]
     * @param string $error   [失败的提示文字]
     * @param string $url     [跳转的URL]
     * @return array
     * @author Mr.Cong <i@cong5.net>
     */
    protected function result($result = '', $success = '', $error = '', $url = '')
    {
        if ($result != false) {
            $data = array(
                'info' => $success,
                'url' => $url,
                'code' => 50001
            );
        } else {
            $data = array(
                'info' => $error,
                'url' => $url,
                'code' => 50002
            );
        }

        return $data;
    }

    /**
     * 当方法为空的时候执行
     * @author Mr.Cong <i@cong5.net>
     */
    public function _empty()
    {
        $_data = array(
            'success' => 50003,
            'info' => 'Access deny'
        );
        $this->response($_data, 'json');
    }

    /**
     * 回应angularJs 跨域中的OPTIONS请求
     * @author Mr.Cong <i@cong5.net>
     */
    public function optionsRequest()
    {
        $data = array(
            'status' => 50001
        );
        $this->response($data,'json');
    }

}