<?php

namespace Common\Model;


class FileModel extends CommonModel
{
    /**
     * 图片上传
     * @author Mr.Cong <i@cong5.net>
     */
    public function uploadPicture()
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