<?php

namespace Api\Model;


use Common\Model\CommonModel;

/**
 * To-do 模型
 * Class TodoModel
 * @package Api\Model
 */
class TodoModel extends CommonModel
{
    /**
     * 数据表名称
     * @tableName string
     */
    protected $tableName = 'Todo';

    /**
     * 自动验证
     * @_validate array
     */
    protected $_validate = array(
        array('todo', 'require', 'To-Do内容不能为空哦！')
    );

    /**
     * 查询字段
     * @field string
     */
    protected $field = 'id,userid,todo,begin_time,end_time,status,create_time,remind';

    /**
     * 获取To-do列表
     * @param array  $map      [查询条件]
     * @param string $order    [排序条件]
     * @param string $field    [查询字段]
     * @param int    $page_num [分页条数]
     * @return array
     * @author Mr.Cong <i@cong5.net>
     */
    public function todoList($map = '', $order = '', $field = '', $page_num = 30)
    {
        $pk = $this->pk; //获取主键
        $map = empty($map) ? $map['id'] = array('gt', 0) : $map;
        $order = empty($order) ? $pk . ' DESC' : $order;
        $field = empty($field) ? $this->field : $field;

        $list = $this->_list($map, $order, $field, $page_num);

        return $list;
    }


    /**
     * 更新Todo
     * @param int    $id
     * @param string $userid
     * @param string $todo
     * @param string $begin_time
     * @param string $end_time
     * @return bool|mixed
     * @author Mr.Cong <i@cong5.net>
     */
    public function createTodo($userid = '', $todo = '', $begin_time = '', $end_time = '')
    {
        $data = array(
            'userid' => $userid,
            'todo' => $todo,
            'begin_time' => $begin_time,
            'end_title' => $end_time,
            'create_time' => time(),
            'remind' => 0
        );

        $res = $this->add($data);

        return $res;
    }

    /**
     * To-Do状态更新
     * @param int    $id     [To-DO ID]
     * @param string $status [To-DO状态]
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function updateTodo($id = 0,$userid = 0, $status = '')
    {
        $data = array(
            'status' => $status,
            'end_time' => time()
        );

        $map = array(
            $this->pk => array('eq', $id),
            'userid' => array('eq',$userid)
        );

        $res = $this->where($map)->save($data);

        return $res;
    }


    /**
     * 删除To-Do
     * @param $id [笔记ID]
     * @return mixed
     * @author Mr.Cong <i@cong5.net>
     */
    public function deleteTodo($id)
    {
        $map = array(
            $this->pk => array('in', $id)
        );

        $res = $this->_delete($map);

        return $res;
    }

}