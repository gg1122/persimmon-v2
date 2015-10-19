<?php
/**
 * Created by PhpStorm.
 * User: MrCong
 * Date: 15/9/3
 * Time: 下午1:25
 */

namespace Common\Model;

class PictureModel extends CommonModel
{

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 文件上传
     * @param  array  $files   要上传的文件列表（通常是$_FILES数组）
     * @param  array  $setting 文件上传配置
     * @param  string $driver  上传驱动名称
     * @param  array  $config  上传驱动配置
     * @return array           文件上传成功后的信息
     */
    public function upload($files, $setting, $driver = 'Local', $config = null)
    {
        /* 上传文件 */
        $setting['callback'] = array($this, 'isFile');
        $setting['removeTrash'] = array($this, 'removeTrash');
        $Upload = new \Think\Upload($setting, $driver, $config);
        $info = $Upload->upload($files);

        if ($info) {
            //文件上传成功，记录文件信息
            foreach ($info as $key => &$value) {
                /* 已经存在文件记录 */
                if (isset($value['id']) && is_numeric($value['id'])) {
                    continue;
                }

                /* 记录文件信息 */
                $value['ext'] = $value['ext'];
                $value['path'] = substr($setting['rootPath'], 1) . $value['savepath'] . $value['savename'];    //在模板里的url路径
                if ($this->create($value) && ($id = $this->add())) {
                    $value['id'] = $id;
                } else {
                    //TODO: 文件上传成功，但是记录文件信息失败，需记录日志
                    unset($info[$key]);
                }
            }
            //生成缩略图
            $thumbInfo = $this->createThumb($info['Filedata']['savepath'],$info['Filedata']['savename'],$info['Filedata']['md5'],$info['Filedata']['sha1']);

            $info['Filedata']['thumb_150x150'] = $thumbInfo['thumb_150x150'];
            $info['Filedata']['thumb_300x300'] = $thumbInfo['thumb_300x300'];

            return $info; //文件上传成功
        } else {
            $this->error = $Upload->getError();
            return false;
        }
    }

    /**
     * 检测当前上传的文件是否已经存在
     * @param  array $file 文件上传数组
     * @return boolean       文件信息， false - 不存在该文件
     */
    public function isFile($file)
    {
        if (empty($file['md5'])) {
            throw new \Exception('缺少参数:md5');
        }
        /* 查找文件 */
        $map = array('md5' => $file['md5'], 'sha1' => $file['sha1'],);
        return $this->field(true)->where($map)->find();
    }

    /**
     * 清除数据库存在但本地不存在的数据
     * @param $data
     */
    public function removeTrash($data)
    {
        $this->where(array('id' => $data['id'],))->delete();
    }

    /**
     * 创建缩略图,传入原图的md5/sha1签名是为了在删除原图的时候,方便一起删除缩略图
     * @param string $savepath  [图片的保存目录]
     * @param string $savename  [原始的图片名字]
     * @param string $imageFile [源图片路径]
     * @param string $md5       [源图片的MD5签名]
     * @param string $sha1      [源图片的SHA1签名]
     * @return array
     * @author Mr.Cong <i@cong5.net>
     */
    public function createThumb($savepath = '', $savename = '', $md5 = '', $sha1 = '')
    {
        $Image = new \Think\Image();

        $path = C('PICTURE_UPLOAD_CONFIG.rootPath') . $savepath;

        $Image->open($path . $savename);

        //生成第一张缩略图
        $thumb_300x300 = $path . 'thumb_300x300_' . $savename;
        $Image->thumb(300, 300, \Think\Image::IMAGE_THUMB_SCALE)->save($thumb_300x300);

        //第二张缩略图
        $thumb_150x150 = $path . 'thumb_150x150_' . $savename;
        $Image->thumb(150, 150, \Think\Image::IMAGE_THUMB_SCALE)->save($thumb_150x150);

        //写入数据库
        $fileData = array(
            array(
                'path' => substr($thumb_150x150, 1),
                'md5' => $md5,
                'sha1' => $sha1,
                'status' => 1,
                'create_time' => time(),
                'ext' => 'jpg'
            ),
            array(
                'path' => substr($thumb_300x300, 1),
                'md5' => $md5,
                'sha1' => $sha1,
                'status' => 1,
                'create_time' => time(),
                'ext' => 'jpg'
            )
        );
        $this->addAll($fileData);

        //返回数据
        $data = array(
            'thumb_150x150' => $thumb_150x150,
            'thumb_300x300' => $thumb_300x300,
        );

        return $data;

    }

}