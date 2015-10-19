<?php

namespace Api\Controller;

/**
 * 笔记API接口
 * Class NotesController
 * @package Note\Controller
 * @author  Mr.Cong <i@cong5.net>
 */
class NotesController extends CommonController
{
    /**
     * 支持的后缀
     * @allowType array
     */
    protected $allowType = array('json');

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
        $this->model = D('Notes');
    }


    /**
     * 单条笔记
     * @author Mr.Cong <i@cong5.net>
     */
    public function oneNote()
    {
        $id = I('id', 0);
        $action = I('action', 'read');

        $map = array(
            'userid' => array('eq',$this->userInfo['uid']),
            'id' => array('eq',$id)
        );

        $noteInfo = $this->model->_get($map);

        //如果action为Edit状态,则替换
        if ('edit' === $action) {
            $noteInfo['content'] = $this->model->prismjs_unreplace($noteInfo['content']);
        }
        //获取用户名,IP地址数据
        $noteInfo['username'] =  D('Common/Users')->get_user_field($noteInfo['userid']);
        $noteInfo['ipAddress'] =  getAddressByIp($noteInfo['client_ip']);

        $this->response($noteInfo, 'json');
    }

    /**
     * 笔记列表
     * @author Mr.Cong <i@cong5.net>
     */
    public function noteList()
    {
        //获取参数
        $order = I('order', '');
        $field = I('field', '');
        $page_num = I('row', '');
        $prams = I('prams', 'inbox');

        //生成where条件
        $map = $this->model->parseMap($prams,$this->userInfo['uid']);

        $list = $this->model->noteList($map, $order, $field, $page_num);

        $count = $this->model->_count($map);

        $data = array(
            'total' => $count,
            'note' => $list
        );

        $this->response($data, 'json');

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
     * 新增
     * @author Mr.Cong <i@cong5.net>
     */
    public function createNote()
    {
        //数据获取和验证
        $this->validate();

        $result = $this->model->updateNote($this->userInfo['uid'],'add');

        $resultData = $this->result($result, "笔记添加成功", "笔记添加失败");

        $this->response($resultData, 'json');
    }

    /**
     * 新增/编辑笔记
     * @author Mr.Cong <i@cong5.net>
     */
    public function updateNote()
    {
        //数据获取和验证
        $this->validate();

        $result = $this->model->updateNote($this->userInfo['uid'],'save');

        $resultData = $this->result($result, "笔记编辑成功", "笔记编辑失败");

        $this->response($resultData, 'json');
    }


    /**
     * 笔记筛选
     * @author Mr.Cong <i@cong5.net>
     */
    public function filter()
    {
        $type = I('type', 'tag');
        $filter = I('keyword');
        switch ($type) {
            case "tag":
                $map = array(
                    'userid' => array('eq',$this->userInfo['uid']),
                    'delete' => array('neq', 1),
                    'tags' => array('like', '%' . $filter . '%')
                );
                $list = $this->model->noteList($map);
                $count = $this->model->_count($map);
                break;
            default:
                ## 还可以按需求扩展按年份、月份等的筛选
                break;
        }
        $data = array(
            'total' => $count,
            'note' => $list
        );

        $this->response($data, 'json');

    }

    /**
     * 删除笔记
     * @author Mr.Cong <i@cong5.net>
     */
    public function deleteNote()
    {
        $map = array(
            'userid' => array('eq',$this->userInfo['uid']),
            'id' => array('eq',I('id', 0))
        );

        $this->model->_delete($map);
    }

    /**
     * 笔记搜索
     * @author Mr.Cong <i@cong5.net>
     */
    public function searchNote()
    {
        //获取参数
        $order = I('order', '');
        $field = I('field', '');
        $page_num = I('page', '');
        $keyword = I('keyword', '');

        //查询条件
        $map = array(
            'userid' => array('eq',$this->userInfo['uid']),
            'delete' => array('neq', 1),
            'title' => array('like', '%' . $keyword . '%'),
        );

        $list = $this->model->noteList($map, $order, $field, $page_num);

        $count = $this->model->_count($map);

        $data = array(
            'total' => $count,
            'note' => $list
        );

        $this->response($data, 'json');
    }

    /**
     * 数据统计
     * @author Mr.Cong <i@cong5.net>
     */
    public function countNote()
    {
        $todoDB = D('Todo');

        //笔记统计
        $NoteMap = array(
            'userid' => array('eq',$this->userInfo['uid']),
            'delete' => array('neq', 1)
        );
        $noteCount = $this->model->_count($NoteMap);

        //To-Do统计
        $todoMap = array(
            'userid' => array('eq',$this->userInfo['uid']),
            'status' => array('eq',0)
        );
        $todoCount = $todoDB->where($todoMap)->count();

        //拼接数据
        $data = array(
            'todo' => $todoCount,
            'note' => $noteCount,
        );

        $this->response($data, 'json');
    }

    /**
     * 笔记存档，按年月来排序
     * @author Mr.Cong <i@cong5.net>
     */
    public function archives()
    {
        //头部
        $output = '<div id="archives"><p>[<a id="al_expand_collapse" href="#">全部展开/收缩</a>] <em>(注: 点击月份可以展开)</em></p>';
        //条件
        $map = array(
            'userid' => array('eq',$this->userInfo['uid']),
            'delete' => array('neq', 1),
        );
        $order = 'id DESC';
        $page_num = 9999;

        $the_query = $this->model->noteList($map, $order, '', $page_num);

        //循环，层级输出
        $year = 0;
        $mon = 0;
        foreach ($the_query as $note) {
            $year_tmp = date('Y', $note['create_time']);
            $mon_tmp = date('m', $note['create_time']);
            if ($mon != $mon_tmp && $mon > 0) $output .= '</ul></li>';
            if ($year != $year_tmp && $year > 0) $output .= '</ul>';
            if ($year != $year_tmp) {
                $year = $year_tmp;
                $output .= '<h3 class="al_year">' . $year . ' 年</h3><ul class="al_mon_list">'; //输出年份
            }
            if ($mon != $mon_tmp) {
                $mon = $mon_tmp;
                $output .= '<li><span class="al_mon">' . $mon . ' 月</span><ul class="al_post_list">'; //输出月份
            }
            $output .= '<li>' . date('d日', $note['create_time']) . '<a href="/#/post/' . $note['id'] . '" target="_blank">' . $note['title'] . '</a> </li>'; //输出文章日期和标题
        }

        $output .= '</ul></li></ul></div>';

        $this->response($output, 'json');
    }

    /**
     * 获取今天天气，分别使用了Taobao的IP查询接口、百度的LBS的简易天气接口
     * @return array|mixed
     * @author Mr.Cong <i@cong5.net>
     */
    public function weather()
    {
        //新从缓存去检查，如果有则取缓存的数据
        $weatherCached = S('weather');
        if ($weatherCached != false) {
            return $weatherCached;
        }

        //获取客户端IP
        $client_ip = get_client_ip();
        //如果是本地，且是DEBUG是TRUE，则把IP替换一下，让其正常显示数据
        if ('127.0.0.1' === $client_ip) {
            $client_ip = '116.10.197.226';
        }

        //根据IP来获取所在城市
        $ip_api = sprintf(C('IPADDR_API'),$client_ip);
        $ipData = curl_request($ip_api);
        $enIpData = json_decode($ipData, true);

        //根据城市来获取天气
        $city = $enIpData['data']['city'];
        $weather_api = C('WEATHER_API') . $city;
        $weatherData = curl_request($weather_api);
        $data = json_decode($weatherData, true);
        $SourceWeather = $data['results'][0]['weather_data'][0];

        //筛选和重组数据
        $weather = array(
            'date' => $SourceWeather['date'],
            'dayPictureUrl' => $SourceWeather['dayPictureUrl'],
            'nightPictureUrl' => $SourceWeather['nightPictureUrl'],
            'weather' => $SourceWeather['weather'],
            'wind' => $SourceWeather['wind'],
            'temperature' => $SourceWeather['temperature']
        );

        S('weather', $weather, $this->cachedOptionsWithRedis);

        $this->response($weather, 'json');

    }

    /**
     * 图片上传
     * @author Mr.Cong <i@cong5.net>
     */
    public function upload()
    {
        //检查是否有图片
        if ($_FILES['upload_file'] == false) {
            $data = array(
                "success" => false,
                "msg" => "没有选择图片",
                "file_path" => ""
            );
            $this->response($data, 'json');
        }

        //配置图片上传参数
        $config = array(
            'maxSize' => 3145728,
            'rootPath' => './Public/uploads/',
            'savePath' => '',
            'saveName' => array('uniqid', ''),
            'exts' => array('jpg', 'gif', 'png', 'jpeg', 'bmp'),
            'autoSub' => true,
            'subName' => array('date', 'Ymd'),
        );

        //上传处理
        $upload = new \Think\Upload($config);
        $info = $upload->upload();
        if (!$info) {
            $data = array(
                'success' => false,
                'msg' => $upload->getError(),
                'file_path' => ''

            );
            $this->response($data, 'json');
            return;
        }

        //保存到数据库
        $field['path'] = '/Public/uploads/' . $info['upload_file']['savepath'] . $info['upload_file']['savename'];
        $field['md5'] = $info['upload_file']['md5'];
        $field['sha1'] = $info['upload_file']['sha1'];
        $field['origin_name'] = str_replace('.' . $info['upload_file']['ext'], '', $info['upload_file']['name']);
        $field['create_time'] = time();
        $field['status'] = '1';
        D('picture')->add($field);
        $data = array(
            'success' => true,
            'msg' => '文件上传成功',
            'file_path' => $config['rootPath'] . $info['upload_file']['savepath'] . $info['upload_file']['savename']

        );
        $this->response($data, 'json');
    }


}