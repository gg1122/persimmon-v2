<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/28
 * Time: 15:16
 */

namespace Api\Model;


use Common\Model\CommonModel;

/**
 * 笔记模型
 * Class NotesModel
 * @package Api\Model
 * @author  Mr.Cong <i@cong5.net>
 */
class AttributeModel extends CommonModel
{

    /**
     * 数据表名称
     * @tableName string
     */
    protected $tableName = 'Attribute';

    /**
     * 自动验证
     * @_validate array
     */
    protected $_validate = array(
        array('name', 'require', '类别类型不能为空！'),
        array('value', 'require', '类别内容不能为空！')
    );

    /**
     * 自动完成
     * @_auto array
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    /**
     * 查询字段
     * @field string
     */
    protected $field = 'id,value';

    public function cateList($map = array(),$order ='',$field ='' ,$page_num = 20)
    {
        $map = empty($map) ? $this->pk.' > 0' : $map;
        $order = empty($order) ? $this->pk.' DESC' : $order;
        $field = empty($field) ? $this->field : $field;

        $list = $this->_list($map,$order,$field,$page_num);

        if($list==false){
            return false;
        }

        return $list;
    }

    /**
     * 新增分类
     * @param int $userid
     * @param string $action
     * @return bool|mixed
     */
    public function cateUpdate($userid = 0,$action = 'add')
    {
        $putData = I('put.','');
        $data = $this->create($putData);
        $data['userid'] = $userid;

        switch($action){
            case 'add':
                $res = $this->add($data);
                break;
            case 'save':
                $map = array(
                    $this->pk => array('eq', $data['id']),
                    'userid' => array('eq',$userid)
                );
                $res = $this->where($map)->save($data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * 删除收藏的网页
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function cateDelete()
    {
        $category = I('category');
        //检查当前类别是否为空
        $checkMap = array(
            'category' => array('eq',$category)
        );
        $checkList = D('Notes')->field('id')->where($checkMap)->select();

        if($checkList!=false){
            $this->error = "删除失败：当前类别下还存在笔记。";
            return false;
        }
        //开始删除
        $map = array(
            'value' => array('eq', $category)
        );

        $res = $this->_delete($map);

        if ($res == false) {
            return false;
        } else {
            return true;
        }
    }

}