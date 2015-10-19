<?php


namespace Common\Model;


/**
 * 评论表模型
 * Class CommentModel
 * @package Common\Model
 */
class CommentModel extends CommonModel
{

    /**
     * 数据库字段
     * @field string
     */
    protected $field = 'id,post_id,comment_pid,comment_name,comment_email,comment_website,comment_content,comment_status,comment_time';
    /**
     * 自动完成
     * @_auto array
     */
    protected $_auto = array(
        array('comment_time','time',self::MODEL_INSERT,'function'),
    );

    /**
     * 自动验证
     * @_validate array
     */
    protected $_validate = array(
        array('comment_name','require','昵称必须填写！'),
        array('comment_email','require','邮箱必须填写！'),
        array('comment_content','require','评论内容不能为空！'),
    );

    /**
     * 获取评论列表
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function commentList($map= array(), $page_num)
    {
        $map = empty($map) ? 'post_id = 1' : $map;

        $list = $this->_list($map,'id DESC',$this->field,$page_num);

        return $list;
    }

    /**
     * 发布评论
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function postComment()
    {
        $data = $this->create();

        if($data== false){
            return false;
        }

        //检查是否是纯英文或者日文的垃圾评论
        if(comment_en_post($data)){
             $this->error = "You should type some Chinese word (like \"你好\") in your comment to pass the spam-check, thanks for your patience! 您的评论中必须包含汉字!";
            return false;
        }
        if(comment_jp_post($data)){
            $this->error = "抱歉,暂时不支持日文的评论. You should type some Chinese word";
            return false;
        }

        $data['comment_content'] = I('comment_content','','html2Text');

        $res = $this->add($data);

        if($res == false){
            return false;
        } else {
            return true;
        }

    }

    /**
     * 删除评论
     * @author Mr.Cong <i@cong5.net>
     */
    public function commentDelete()
    {
        $id = I('id',0);

        $map = array(
            $this->pk => array('in',$id)
        );

        $res = $this->where($map)->delete();

        if($res == false){
            return false;
        } else {
            return true;
        }
    }

    /**
     * 评论审核
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function commentCheck()
    {
        $id = I('id',0);

        $map = array(
            $this->pk => array('in',$id)
        );

        $res = $this->where($map)->setField('comment_status',0);

        if($res == false){
            return false;
        } else {
            return true;
        }
    }

}