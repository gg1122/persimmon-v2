<?php

namespace Common\Model;


use Think\Model;

/**
 * 公共模型类
 * Class CommonModel
 * @package Api\Model
 */
class CommonModel extends Model
{
    /**
     * 获取数据列表
     * @param array  $map      [查询条件]
     * @param string $order    [排序条件]
     * @param string $fields   [查询字段]
     * @param int    $page_num [分页条数]
     * @return array
     * @author Mr.Cong <i@cong5.net>
     */
    public function _list($map = '', $order = '', $fields = '', $page_num = '')
    {
        //获取分页
        $count = $this->_count($map);

        $Page = new \Think\Page($count, $page_num);

        $list = $this->where($map)->field($fields)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $list = empty($list) ? array() : $list;

        return $list;
    }

    /**
     * 获取总数
     * @param $map [条件]
     * @return int
     * @author Mr.Cong <i@cong5.net>
     */
    public function _count($map = array())
    {
        $count = $this->where($map)->count();

        $count = empty($count) ? 0 : $count;

        return $count;
    }

    /**
     * 获取单条数据
     * @param $id [笔记ID]
     * @return array
     * @author Mr.Cong <i@cong5.net>
     */
    public function _get($map = array())
    {
        $note = $this->where($map)->find();

        $note = empty($note) ? array() : $note;

        return $note;
    }

    /**
     * 删除数据
     * @param $map array [ID]
     * @return mixed
     * @author Mr.Cong <i@cong5.net>
     */
    public function _delete($map = array())
    {

        $res = $this->where($map)->delete();

        return $res;
    }

    /**
     * 获取分页数据
     * @param array $map      [条件]
     * @param int   $page_num [分页数量]
     * @author Mr.Cong <i@cong5.net>
     */
    public function _page($map = array(), $page_num = 20)
    {
        $count = $this->_count($map);

        $Page = new \Think\Page($count, $page_num);

        $show = $Page->show();

        return $show;

    }

}