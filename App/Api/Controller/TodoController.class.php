<?php

namespace Api\Controller;


/**
 * To-DO 控制器
 * Class TodoController
 * @package Api\Controller
 */
class TodoController extends CommonController
{
    /**
     * 数据模型
     * @model
     */
    protected $model;

    /**
     * 初始化方法
     * @author Mr.Cong <i@cong5.net>
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = D('Todo');
    }

    /**
     * 还没开始的To-Do
     * @author Mr.Cong <i@cong5.net>
     */
    public function todoList()
    {
        $userid = $this->userInfo['uid'];

        if($userid == false){
            $data = array(
                'code' => 50002,
                'info' => '用户ID不能为空'
            );
            $this->response($data);
            exit();
        }

        $status = I('status',0);

        $map = array(
            'status' => array('eq',$status),
            'userid' => array('eq',$userid)
        );

        $list = $this->model->todoList($map);

        $total = $this->model->_count($map);

        $data = array(
            'list' => $list,
            'total' => $total
        );

        $this->response($data,'json');

    }


    /**
     * 创建To-Do
     * @author Mr.Cong <i@cong5.net>
     */
    public function createTodo()
    {

        $userId = $this->userInfo['uid'];

        $todo = I('todo');

        $result = $this->model->createTodo($userId, $todo);

        $resultData = $this->result($result, "Todo新增成功", "Todo新增失败");

        $this->response($resultData, 'json');
    }

    /**
     * 更新To-do
     * @author Mr.Cong <i@cong5.net>
     */
    public function updateTodo()
    {
        $status = I('status');

        $userId = $this->userInfo['uid'];

        $id = I('id');

        $result = $this->model->updateTodo($id, $userId, $status);

        $resultData = $this->result($result, "Todo更新成功", "Todo更新失败");

        $this->response($resultData, 'json');

    }

    /**
     * To-do 删除
     * @author Mr.Cong <i@cong5.net>
     */
    public function deleteTodo()
    {
        $id = I('id',0);

        $result = $this->model->deleteTodo($id);

        $resultData = $this->result($result, "Tod删除成功", "Todo删除失败");

        $this->response($resultData, 'json');

    }
}