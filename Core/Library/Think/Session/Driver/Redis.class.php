<?php
/**
 * Created by PhpStorm.
 * User: Mr.Cong <i@cong5.net>
 * Date: 2015/8/20
 * Time: 11:11
 */

namespace Think\Session\Driver;


class Redis
{

    /**
     * Redis连接对象
     * @redis
     */
    private $redis;

    /**
     * Session过期时间
     * @expire
     */
    private $expire;


    /**
     * 打开方法
     * @param $path [SESSION保存路径]
     * @param $name [SESSION名称]
     * @return mixed
     * @author Mr.Cong <i@cong5.net>
     */
    public function open($path, $name)
    {
        $this->expire = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : ini_get('session.gc_maxLifetime');
        $this->redis = new \Redis();
        return $this->redis->connect(C('REDIS_HOST'), C('REDIS_PORT'));
    }


    /**
     * 关闭
     * @return mixed
     * @author Mr.Cong <i@cong5.net>
     */
    public function close()
    {
        return $this->redis->close();
    }


    /**
     * 读取
     * @param $id
     * @return string
     * @author Mr.Cong <i@cong5.net>
     */
    public function read($id)
    {
        $id = C('SESSION_PREFIX') . $id;
        $data = $this->redis->get($id);
        return $data ? $data : '';
    }


    /**
     * 写入
     * @param $id   [Redis保存的key]
     * @param $data [Redis保存的value]
     * @return mixed
     * @author Mr.Cong <i@cong5.net>
     */
    public function write($id, $data)
    {
        $id = C('SESSION_PREFIX') . $id;
        return $this->redis->set($id, $data, $this->expire);
    }


    /**
     * 销毁
     * @param $id [Redis保存的key]
     * @author Mr.Cong <i@cong5.net>
     */
    public function destroy($id)
    {
        $id = C('SESSION_PREFIX') . $id;
        $this->redis->delete($id);
    }


    /**
     * 垃圾回收
     * @param $maxLifeTime
     * @return bool
     * @author Mr.Cong <i@cong5.net>
     */
    public function gc($maxLifeTime)
    {
        return true;
    }

}