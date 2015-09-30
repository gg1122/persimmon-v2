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
     * 模型对象
     * @Model
     */
    protected $model;

    /**
     * 初始化数据模型
     * @author Mr.Cong <i@cong5.net>
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = D('Attribute');
    }

    /**
     * 类别列表
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
     * 数据自动获取和验证
     * @author Mr.Cong <i@cong5.net>
     */
    protected function validate()
    {
        //根据数据的提交方式来获取和验证数据
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
     * 新增类别
     */
    public function addCate()
    {
        $this->validate();

        $result = $this->model->cateUpdate($this->userInfo['uid'],'add');

        $resultData = $this->result($result, "类别添加成功", "类别添加失败");

        $this->response($resultData, 'json');
    }

    /**
     * 编辑类别
     */
    public function editCate()
    {
        $this->validate();

        $result = $this->model->cateUpdate($this->userInfo['uid'],'edit');

        $resultData = $this->result($result, "类别编辑成功", "类别编辑失败");

        $this->response($resultData, 'json');
    }

    /**
     * 删除类别
     */
    public function deleteCate()
    {
        $map = array(
            'userid' => array('eq',$this->userInfo['uid']),
            'id' => array('eq',I('id', 0))
        );

        $result = $this->model->_delete($map);

        $resultData = $this->result($result, "类别删除成功", "类别删除失败");

        $this->response($resultData, 'json');
    }

}