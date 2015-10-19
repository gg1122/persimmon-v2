<?php
/**
 * Created by PhpStorm.
 * User: MrCong
 * Date: 15/9/11
 * Time: 下午9:48
 */

namespace Api\Controller;

/**
 * Class FavController
 * @package Api\Controller
 */
class FavController extends CommonController
{
    /**
     * 实例化数据表对象
     * @model
     */
    protected $model;

    /**
     *初始化，定义主题目录
     */
    public function _initialize()
    {
        header('Access-Control-Allow-Origin:*');
        $this->model = D('Favorites');
    }

    /**
     * @author Mr.Cong <i@cong5.net>
     */
    public function favList()
    {

        $map = array(
            'userid' => array('eq',$this->userInfo['uid'])
        );

        $list = $this->model->getList($map);

        $count = $this->model->_count($map);

        $data = array(
            'list' => $list,
            'total' => $count,
            'page' => ceil($count / 20)
        );

        $this->response($data, 'json');
    }

    /**
     * 添加收藏
     * @author Mr.Cong <i@cong5.net>
     */
    public function post()
    {
        $res = $this->model->update();

        if ($res == false) {
            $data = array(
                'status' => 0,
                'info' => $this->model->getError()
            );
        } else {
            $data = array(
                'status' => 1,
                'info' => '收藏成功'
            );
        }

        $this->response($data, 'json');
    }

    /**
     * 收藏夹搜索
     * @author Mr.Cong <i@cong5.net>
     */
    public function search()
    {
        $keyword = I('word','');

        if($keyword==''){
            $data = array(
                'status' => 0,
                'info' => '关键词不能为空'
            );

            $this->response($data, 'json');
            exit();
        }

        $map = array(
            'userid' => array('eq',$this->userInfo['uid']),
            'title' => array('like','%'.$keyword.'%')
        );

        $list = $this->model->getList($map);

        $count = $this->model->_count($map);

        $data = array(
            'list' => $list,
            'total' => $count,
            'page' => ceil($count / 20)
        );

        $this->response($data, 'json');
    }

    /**
     * 删除收藏夹信息
     * @author Mr.Cong <i@cong5.net>
     */
    public function delete()
    {
        $res = $this->model->favDelete($this->userInfo['uid']);

        if ($res == false) {
            $data = array(
                'status' => 0,
                'info' => $this->model->getError()
            );
        } else {
            $data = array(
                'status' => 1,
                'info' => '收藏删除成功'
            );
        }

        $this->response($data, 'json');
    }

    /**
     * 根据网址创建缩略图
     * @author Mr.Cong <i@cong5.net>
     */
    public function createThumb()
    {
        $id = I('id',0);

        $target_url = I('target_url','');

        $parse_url = parse_url($target_url);

        $newFileName = $parse_url['host'] . '@' . time() . '.jpg';

        $path = './Public/fav-thumb/' . date('Ym') . '/';

        if (!is_dir($path)) {
            mkdir($path);
        }

        $snapshot = $this->model->createThumbByPage2imageApi($target_url);

        if($snapshot==false){
            echo $this->model->getError();
        }

        $content = curl_request($snapshot->image_url);

        $fileName = $path.$newFileName;

        file_put_contents($fileName,$content);

        $map = array(
            'id' => array('eq',$id)
        );

        $this->model->where($map)->setField('thumb',$fileName);

        echo '0';
    }

}