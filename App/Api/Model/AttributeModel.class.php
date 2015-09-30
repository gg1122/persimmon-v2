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
 * �ʼ�ģ��
 * Class NotesModel
 * @package Api\Model
 * @author  Mr.Cong <i@cong5.net>
 */
class AttributeModel extends CommonModel
{

    /**
     * ���ݱ�����
     * @tableName string
     */
    protected $tableName = 'Attribute';

    /**
     * �Զ���֤
     * @_validate array
     */
    protected $_validate = array(
        array('name', 'require', '������Ͳ���Ϊ�գ�'),
        array('value', 'require', '������ݲ���Ϊ�գ�')
    );

    /**
     * �Զ����
     * @_auto array
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    /**
     * ��ѯ�ֶ�
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
     * ��������
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
     * ɾ���ղص���ҳ
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function cateDelete()
    {
        $category = I('category');
        //��鵱ǰ����Ƿ�Ϊ��
        $checkMap = array(
            'category' => array('eq',$category)
        );
        $checkList = D('Notes')->field('id')->where($checkMap)->select();

        if($checkList!=false){
            $this->error = "ɾ��ʧ�ܣ���ǰ����»����ڱʼǡ�";
            return false;
        }
        //��ʼɾ��
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