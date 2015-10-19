<?php

namespace Api\Model;


use Common\Model\CommonModel;

/**
 * 笔记模型
 * Class NotesModel
 * @package Api\Model
 * @author  Mr.Cong <i@cong5.net>
 */
class NotesModel extends CommonModel
{

    /**
     * 数据表名称
     * @tableName string
     */
    protected $tableName = 'Notes';

    /**
     * 自动验证
     * @_validate array
     */
    protected $_validate = array(
        array('title', 'require', '笔记标题不能为空哦！'),
        array('content', 'require', '笔记内容不能为空哦！')
    );

    /**
     * 自动完成
     * @_auto array
     */
    protected $_auto = array(
        array('create_time','time',self::MODEL_INSERT,'function'),
        array('update_time','time',self::MODEL_UPDATE,'function'),
        array('client_ip','get_client_ip',self::MODEL_BOTH,'function'),
        array('delete','0'),
        array('remind','0'),
    );

    /**
     * 查询字段
     * @field string
     */
    protected $field = 'id,userid,title,tags,content,delete,client_ip,remind,category,create_time,update_time';

    /**
     * 获取笔记列表
     * @param array $map [查询条件]
     * @param string $order [排序条件]
     * @param string $field [查询字段]
     * @param int $page_num [分页条数]
     * @return array
     * @author Mr.Cong <i@cong5.net>
     */
    public function noteList($map = array(), $order = '', $field = '', $page_num = 30)
    {
        $pk = $this->pk; //获取主键
        $map = empty($map) ? $map['id'] = array('gt', 0) : $map;
        $order = empty($order) ? $pk . ' DESC' : $order;
        $field = empty($field) ? $this->field : $field;

        $list = $this->_list($map, $order, $field, $page_num);

        return $list;
    }

    /**
     * 解析查询条件
     * @param $prams [笔记类型：inbox,notes,delete,knowledge]
     * @param $uid   [用户id]
     * @return array
     * @author Mr.Cong <i@cong5.net>
     */
    public function parseMap($prams, $uid)
    {
        $map = array(
            'category' => array('eq', trim($prams)),
            'delete' => array('neq', 1),
            'userid' => array('eq', $uid)
        );

        $map = empty($map) ? array() : $map;

        return $map;

    }


    /**
     * 新增/编辑笔记
     * @param int $id [笔记ID]
     * @param string $userid [用户ID]
     * @param string $action [操作方法]
     * @return bool|mixed
     * @author Mr.Cong <i@cong5.net>
     */
    public function updateNote($userid = '1',$action = 'add')
    {
        $putData = I('put.','');
        $data = $this->create($putData);
        $data['userid'] = $userid;
        $data['content'] = $this->prismjs_replace(I('content','',''));

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
     * 保存进数据库前的正则替换
     * @param $html [内容]
     * @return string
     * @author Mr.Cong <i@cong5.net>
     */
    public function prismjs_replace($html)
    {
        $pattern = "/<pre class=\"lang\-(\w+)\" data-lang=\"(\w+)\">([\s|\S]*?)<\/pre>/i";

        $replacement = "<pre class=\"line-numbers\"><code class=\"language-$1\" prism>$3</code></pre>";

        $content = preg_replace($pattern, $replacement, $html);

        return $content;
    }


    /**
     * 编辑的时候正则替换回默认
     * @param $html [内容]
     * @return string
     * @author Mr.Cong <i@cong5.net>
     */
    public function prismjs_unreplace($html)
    {

        $pattern = "/<pre class=\"line\-numbers\"><code class=\"language\-(\w+)\" prism>([\s|\S]*?)<\/code><\/pre>/i";

        $replacement = "<pre class=\"lang-$1\" data-lang=\"$1\">$2</pre>";

        $content = preg_replace($pattern, $replacement, $html);

        return $content;
    }

}