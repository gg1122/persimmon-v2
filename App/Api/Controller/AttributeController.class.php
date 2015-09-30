<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/28
 * Time: 15:13
 */

namespace Api\Controller;


class AttributeController extends CommonController
{

    /**
     * ģ�Ͷ���
     * @Model
     */
    protected $model;

    /**
     * ��ʼ������ģ��
     * @author Mr.Cong <i@cong5.net>
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = D('Attribute');
    }

    /**
     * ����б�
     */
    public function index()
    {
        $map = array(
            'userid' => array('eq',$this->userInfo['uid'])
        );
        $list = $this->model->cateList($map,'','',999);

        if($list==false){
            $data = array(
                'code' => 50002,
                'info' => $this->model->getError()
            );
            $this->response($data,'json');
            exit();
        }

        $data = array(
            'code' => 50001,
            'cate' => $list
        );

        $this->response($data,'json');
    }

    /**
     * �����Զ���ȡ����֤
     * @author Mr.Cong <i@cong5.net>
     */
    protected function validate()
    {
        //�������ݵ��ύ��ʽ����ȡ����֤����
        switch($this->_method){
            case 'post':
                if(!$this->model->create()){
                    $tipsMsg = array(
                        'info'=>$this->model->getError(),
                        'code'=>50002
                    );
                    $this->response($tipsMsg,'json');
                }
                break;
            case 'put':
                $data = I('put.', '', '');
                if(!$this->model->create($data)){
                    $tipsMsg = array(
                        'info'=>$this->model->getError(),
                        'code'=>50002
                    );
                    $this->response($tipsMsg,'json');
                }
                break;
            default:;
        }
    }

    /**
     * �������
     */
    public function addCate()
    {
        $this->validate();

        $result = $this->model->cateUpdate($this->userInfo['uid'],'add');

        $resultData = $this->result($result, "�����ӳɹ�", "������ʧ��");

        $this->response($resultData, 'json');
    }

    /**
     * �༭���
     */
    public function editCate()
    {
        $this->validate();

        $result = $this->model->cateUpdate($this->userInfo['uid'],'edit');

        $resultData = $this->result($result, "���༭�ɹ�", "���༭ʧ��");

        $this->response($resultData, 'json');
    }

    /**
     * ɾ�����
     */
    public function deleteCate()
    {
        $map = array(
            'userid' => array('eq',$this->userInfo['uid']),
            'id' => array('eq',I('id', 0))
        );

        $result = $this->model->_delete($map);

        $resultData = $this->result($result, "���ɾ���ɹ�", "���ɾ��ʧ��");

        $this->response($resultData, 'json');
    }

}