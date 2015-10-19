<?php

namespace Note\Controller;

use Think\Controller;

/**
 * 空控制器
 * Class EmptyController
 * @package Note\Controller
 */
class EmptyController extends Controller
{
    public function index()
    {
        $_data = array(
            'success' => 10004,
            'info' => 'Access deny'
        );
        $this->ajaxReturn($_data, 'json');
    }
}