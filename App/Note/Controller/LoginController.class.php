<?php

namespace Note\Controller;

use Think\Controller;

/**
 * 登陆控制器
 * Class IndexController
 * @package Login\Controller
 * @author  Mr.Cong <i@cong5.net>
 */
class LoginController extends Controller
{
    /**
     * 新浪微博的APP ID
     * @weibo_akey
     */
    private $weibo_akey;
    /**
     * 新浪微博的SECURE KEY
     * @weibo_skey
     */
    private $weibo_skey;

    private $Redis;

    /**
     * 初始化
     * @author Mr.Cong <i@cong5.net>
     */
    public function _initialize()
    {
        //构造微博的KEY
        $this->weibo_akey = C('WB_AKEY');
        $this->weibo_skey = C('WB_SKEY');
        Vendor('libweibo.SaeTOAuthV2');
        //实例化Redis
        $expire = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : '3600';
        $this->Redis = new \Redis();
        $this->Redis->connect(C('REDIS_HOST'), C('REDIS_PORT'),$expire);
    }

    /**
     * 登陆页面
     * @author Mr.Cong <i@cong5.net>
     */
    public function index()
    {
        $callback = I('callback','','urlencode');
        if($callback!=false){
            $this->Redis->set('callback',$callback,30);
        }
        //微博登陆
        $auth = new \SaeTOAuthV2($this->weibo_akey, $this->weibo_skey);
        $weibo_login_url = $auth->getAuthorizeURL(C('WB_CALLBACK_URL'));
        header('Location:'.$weibo_login_url);
    }

    /**
     * 退出登陆
     * @author Mr.Cong <i@cong5.net>
     */
    public function logout()
    {
        $ticket = cookie('ticket');
        $this->Redis->delete(json_decode($ticket));
        cookie('ticket',null);
        cookie('user',null);
        $data = array(
            'code' => 50001,
            'info' => '注销成功'
        );
        $this->ajaxReturn($data,'json');
    }

    /**
     * 微博登陆成功回调函数
     * @author Mr.Cong <i@cong5.net>
     */
    public function auth()
    {
        header("Content-type:text/html;charset=utf-8");

        $OAuth = new \SaeTOAuthV2($this->weibo_akey, $this->weibo_skey);

        //获取回调中的code参数，再去获取Token
        if (I('get.code', '') != '') {
            $keys = array(
                'code' => I('get.code'),
                'redirect_uri' => C('WB_CALLBACK_URL')
            );
            $token = $OAuth->getAccessToken('code', $keys);
        }

        //如果有Token的话，则写入Session和Cookie
        if ($token != false) {
            $_SESSION['token'] = $token;
            setcookie('weibojs_' . $OAuth->client_id, http_build_query($token));
        } else {
            $this->show("<script>alert('授权失败');window.location.href='" . C('LOGIN_URL') . "'</script>");
            exit();
        }

        //进入检查用户信息
        $this->checkLogin();

    }

    /**
     * 检查用户登陆
     * @param $userInfo
     * @return array
     */
    public function checkLogin()
    {
        //获取微博用户信息（根据ID获取用户等基本信息）
        $Client = new \SaeTClientV2($this->weibo_akey, $this->weibo_skey, $_SESSION['token']['access_token']);
        $uid_get = $Client->get_uid();
        $weiboUser = $Client->show_user_by_id($uid_get['uid']);

        $model = D('Common/Users');

        $userInfo = $model->login($weiboUser['name']);

        //如果没有用户信息的，则新增用户
        if ($userInfo === false) {
            $this->createUser($weiboUser);
        }

        cookie('ticket',json_encode($userInfo['ticket']));
        $cookie = array(
            'avatar' => $userInfo['weibo_avatar'],
            'username' => $userInfo['username']
        );
        notPreCookie('user',$cookie);

        $this->Redis->set($userInfo['ticket'],serialize($userInfo),C('COOKIE_EXPIRE'));

        //写日志
        D('Common/LoginLog')->writeLog($userInfo['username']);

        $callback = $this->Redis->get('callback');

        if($callback!=false){
            header("Location:".base64_decode(urldecode($callback)));
        } else {
            header("Location:/");
        }
    }

    /**
     * 根据新浪微博返回的信息创建用户
     * @param $weibo_id          [微博ID]
     * @param $weibo_name        [微博名称]
     * @param $weibo_description [描述]
     * @param $weibo_address     [所在城市]
     * @param $weibo_avatar      [微博头像]
     * @param $weibo_domain      [微博域名]
     * @return bool|int
     * @author Mr.Cong <i@cong5.net>
     */
    public function createUser($weiboUser)
    {
        $model = D('Common/Users');

        $avatar = $this->get_avatar($weiboUser['id'],$weiboUser['avatar_hd']);

        $addPK = $model->createUserByWeibo(
            $weiboUser['id'],
            $weiboUser['name'],
            $weiboUser['description'],
            $weiboUser['location'],
            $avatar,
            $weiboUser['domain']
        );

        if ($addPK === false) {
            $this->error($model->getError());
        }

        $userInfo = $model->_get($addPK);

        cookie('ticket',json_encode($userInfo['ticket']));
        $cookie = array(
            'avatar' => $userInfo['weibo_avatar'],
            'username' => $userInfo['username']
        );
        notPreCookie('user',$cookie);


        $this->Redis->set($userInfo['ticket'],serialize($userInfo),C('COOKIE_EXPIRE'));

        //写日志
        D('Common/LoginLog')->writeLog($userInfo['username']);

        $callback = $this->Redis->get('callback');

        if($callback!=false){
            header("Location:".base64_decode(urldecode($callback)));
        } else {
            header("Location:/");
            exit();
        }
    }


    /**
     * 获取微博头像,并缓存
     * @param $uid
     * @param $url
     * @return bool|string
     * @author Mr.Cong <i@cong5.net>
     */
    private function get_avatar($uid,$url)
    {
        if (strpos($url, 'http://')!==false) {
            $f = md5($uid);
            $e = './Public/avatar/' . $f . '.jpg';
            $t = 1209600; //設定14天, 單位:秒
            if (!is_file($e) || (time() - filemtime($e)) > $t) { //當頭像不存在或文件超過14天才更新
                $stream = curl_request($url);
                file_put_contents($e,$stream);
            } else {
                $avatar = '/Public/avatar/'.$f.'.jpg';
            }
            if (filesize($e) < 500){
                $avatar = '/Public/avatar/default.jpg';
            }
            return $avatar;
        }
        return false;
    }

}