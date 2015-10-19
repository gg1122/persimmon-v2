<?php

namespace Api\Controller;

use Think\Controller;

/**
 * 空控制器
 * Class EmptyController
 * @package Api\Controller
 * @author  Mr.Cong <i@cong5.net>
 */
class EmptyController extends Controller
{
    /**
     * 当控制器不存在的时候执行
     * @author Mr.Cong <i@cong5.net>
     */
    public function index()
    {
        $_data = array(
            'success' => 10004,
            'info' => 'Access deny'
        );
        $this->ajaxReturn($_data, 'json');
    }
}