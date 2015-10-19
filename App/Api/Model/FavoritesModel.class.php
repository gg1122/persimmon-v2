<?php

namespace Api\Model;


use Common\Model\CommonModel;

class FavoritesModel extends CommonModel
{

    /**
     * 查询的字段
     * @field string
     */
    protected $field = 'id,userid,title,tags,source,snapshot,thumb,create_time,update_time';

    /**
     * 自动完成
     * @_auto array
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
        array('tags', 'strtolower', self::MODEL_BOTH, 'function'),
    );

    /**
     * 字段验证
     * @_validate array
     */
    protected $_validate = array(
        array('title', 'require', '标题必须！'),
        array('source', 'require', 'URL地址必须'),
    );

    /**
     * 获取列表
     * @param array  $map      [查询条件]
     * @param string $order    [排序条件]
     * @param string $field    [查询的字段]
     * @param int    $page_num [每页最大条数]
     * @return array
     * @author Mr.Cong <i@cong5.net>
     */
    public function getList($map = array(), $order = '', $field = '', $page_num = 20)
    {
        $map = empty($map) ? 'id > 0' : $map;
        $order = empty($order) ? 'id DESC' : $order;
        $field = empty($field) ? $this->field : $field;

        $list = $this->_list($map, $order, $field, $page_num);

        return $list;
    }

    /**
     * 新增/编辑收藏的网页
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function update()
    {

        $data = $this->create();

        if ($data == false) {
            return false;
        }

        $res = $this->add($data);

        if ($res == false) {
            return false;
        }

        //self::createFsockopen($data['source'], $this->getLastInsID());

        return true;

    }

    /**
     * 使用fsockopen函数打开一个网络连接来生成缩略图,解决PHP无法异步生成缩略图的问题
     * @param $target_url  [目标地址]
     * @author Mr.Cong <i@cong5.net>
     */
    static public function createFsockopen($target_url, $id)
    {
        //生成缩略图
        $param = array(
            'target_url' => $target_url,
            'id' => $id
        );

        $host = C('API_DOMAIN') . '/v2/fav/thumb?' . http_build_query($param);

        $fp = fsockopen($host, 80, $errno, $errstr, 30);
        if (!$fp) {
            $data = array(
                'errno' => $errno,
                'errstr' => $errstr
            );
            write_log($data);
        } else {
            $out = "GET / HTTP/1.1\r\n";
            $out .= "Host: api.cong5.net\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            fclose($fp);
        }
    }


    /**
     * 删除收藏的网页
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function favDelete($uid)
    {
        $id = I('id', 0);

        $favInfo = $this->_get($id);

        if(!empty($favInfo['thumb'])){
            @unlink($favInfo['thumb']);
        }

        $map = array(
            $this->pk => array('in', $id),
            'userid' => array('eq',$uid)
        );

        $res = $this->_delete($map);

        if ($res == false) {
            return false;
        } else {
            return true;
        }

    }


    /**
     * 根据网址来生成缩略图；使用第三方工具 http://phantomjs.org/
     * @param string $target_url [目标网址]
     * @param string $target_url [图片保存路径]
     * @param string $target_url [图片文件名]
     * @return array|bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function PhantomJSCreateThumb($target_url = '', $path = '',$fileName = '')
    {

        $filePath = $path . $fileName;
        //拼接命令
        $shell = sprintf(C('PHANTOMJS'), $target_url, $filePath);
        //过滤
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $escaped_command = $shell;
        } else if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN' || strtoupper(substr(PHP_OS, 0, 3)) === 'DAR') {
            $escaped_command = escapeshellcmd($shell);
        }

        //执行shell代码
        @exec($escaped_command, $arr, $status);
        //失败处理结果
        if ($status !== 0) {
            $data = array(
                'status' => $status,
                'result' => $arr
            );
            write_log($data);
            return false;
        }
        //成功的处理结果
        if (class_exists("Imagick")) {
            $image = new \Think\Image(\Think\Image::IMAGE_IMAGICK); //使用IMAGICK
        } else {
            $image = new \Think\Image(); //使用GD
        }

        $image->open($filePath);

        $image->thumb(550, 275, \Think\Image::IMAGE_THUMB_NORTHWEST)->save($filePath);

        return $filePath;

    }

    /**
     * 根据Page2image 的API来创建缩略图
     * @param string $target_url [目标网址]
     * @return bool|string
     */
    public function createThumbByPage2imageApi($target_url)
    {
        if (empty($target_url)) {
            $this->error = '目标地址不能为空';
            return false;
        }
        $target_url = sprintf(C('PAGE2IMAGE_API'), $target_url);
        $loop_flag = true;
        $snapshot = '';
        $timeout = 120;
        set_time_limit($timeout + 10);
        $start_time = time();
        $timeout_flag = false;

        while ($loop_flag) {

            $response = curl_request($target_url);

            //当没有响应的时候的出来
            if (empty($response)) {
                $loop_flag = false;
                $this->error = '远程服务器没有响应';
                break;
            } else {
                $json_data = json_decode($response);
                if (empty($json_data->status)) {
                    $loop_flag = false;
                    $this->error = 'API接口错误';
                    break;
                }
            }
            //根据状态来做判断
            switch ($json_data->status) {
                case "error":
                    $loop_flag = false;
                    $this->error = $json_data->errno . " " . $json_data->msg;
                    break;
                case "finished":
                    $loop_flag = false;
                    return $json_data;
                    break;
                case "processing":
                default:
                    if ((time() - $start_time) > $timeout) {
                        $loop_flag = false;
                        $timeout_flag = true;
                    } else {
                        sleep(3);
                    }
                    break;
            }
        }
        //超时的时候提示错误
        if ($timeout_flag) {
            $this->error = "额，超时" . $timeout . "秒.";
        }

        return false;

    }

}