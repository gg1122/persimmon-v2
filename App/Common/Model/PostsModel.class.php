<?php

namespace Common\Model;


class PostsModel extends CommonModel
{
    /**
     * 数据表名称
     * @tableName string
     */
    protected $tableName = 'Posts';

    /**
     * 查询字段
     * @field string
     */
    protected $field = 'id,userid,title,description,content,tags,cat_id,create_time,update_time,client_ip,status';

    /**
     * 自动完成
     * @_auto array
     */
    protected $_auto = array(
        array('create_time','time',1,'function'),
        array('client_ip','get_client_ip',1,'function'),
        array('userid','get_uid',1,'function'),
        array('update_time','time',2,'function')
    );

    /**
     * 查询单条数据
     * @param $id
     * @author Mr.Cong <i@cong5.net>
     */
    public function one($id)
    {
        return $this->_get($id);
    }

    /**
     * 获取博文列表
     * @param array  $map      [查询条件]
     * @param string $order    [排序条件]
     * @param string $field    [查询字段]
     * @param int    $page_num [分页条数]
     * @return array
     * @author Mr.Cong <i@cong5.net>
     */
    public function getList($map = array(), $order = '', $field = '', $page_num = 20)
    {

        $map = empty($map) ? $map['id'] = array('eq', 0) : $map;
        $order = empty($order) ? $this->pk . ' DESC' : $order;
        $field = empty($field) ? $this->field : $field;
        $page_num = empty($page_num) ? 20 : $page_num;

        $list = $this->_list($map, $order, $field, $page_num);

        return $list;
    }

    /**
     * 获取总数
     * @param array $map [条件]
     * @return int
     * @author Mr.Cong <i@cong5.net>
     */
    public function countData($map = array())
    {
        return $this->_count($map);
    }

    /**
     * 新增/编辑文章
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function update()
    {
        $data = $this->create();
        $data['description'] = I('description', '', 'html2Text');
        $data['content'] = I('content', '', '');
        $data['cat_id'] = implode(',', I('cat_id'));

        if ($data['id'] != false) {
            $res = $this->save($data);
        } else {
            $res = $this->add($data);
        }

        if ($res == false) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * 删除文章/缩略图
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function deletePost()
    {
        $id = I('id',0);

        if($id == false){
            $this->error = 'ID不能为空';
            return false;
        }

        $map = array(
            $this->pk => array('in',$id)
        );

        //删除缩略图
        $thumbList = $this->field('thumb')->where($map)->select();

        foreach($thumbList as $key=>$thumbs){
            if($thumbs['thumb']!=false){
                @unlink('.'.$thumbs['thumb']);
            }
        }

        //删除文章
        $res = $this->where($map)->delete();

        if ($res == false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 移除文章到回收站
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function trash()
    {
        $id = I('id',0);

        $status = I('status',0);

        $data = array(
            'status' => $status
        );

        $map = array(
            $this->pk => array('in',$id)
        );

        $res = $this->where($map)->save($data);

        if ($res == false) {
            return false;
        } else {
            return true;
        }

    }

}