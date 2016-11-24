<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Wechat
{
    var $_appID="wx24e254d9de36120a";//培训派//传师wx2736e8f78ee6a1a0
    var $_appsecret="4e2d115891abc0c93bd4eaccaacc9e11";//培训派//传师5dcbec418c147ecf5d94f5221f20fdfb
    var $_access_token;
    var $_token_file;
    var $_jsapi_ticket_file;
    var $_jsapi_ticket;

    function __construct($companyToken)
    {
        if(!empty($companyToken['appid'])&&!empty($companyToken['appsecret'])){
            $this->_appID=$companyToken['appid'];
            $this->_appsecret=$companyToken['appsecret'];
            $this->_token_file = str_replace('html/wxpai','html/pai', dirname(__FILE__)) . '/access/access_token'.$companyToken['company_code'].'.wx';
        }else{
            $this->_token_file = str_replace('html/wxpai','html/pai', dirname(__FILE__)) . '/access/access_token.wx';
        }
        $ctime = filectime($this->_token_file);
        $this->_access_token = file_get_contents($this->_token_file);
        if (empty($this->_access_token) || (time() - $ctime) >= 7200) {
            $this->setToken();
        }

        $this->_jsapi_ticket_file = dirname(__FILE__) . '/jsapi_ticket.wx';
        $jsctime = filectime($this->_jsapi_ticket_file);
        $this->_jsapi_ticket = file_get_contents($this->_jsapi_ticket_file);
        if (empty($this->_jsapi_ticket) || (time() - $jsctime) >= 7200) {
            $this->setJsticket();
        }
    }

    private function __clone()
    {
    }


    function setToken()
    {
        $tokenurl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->_appID . "&secret=" . $this->_appsecret;
        $data = $this->returnWeChatJsonData($tokenurl);
        $this->_access_token = $data->access_token;
        file_put_contents($this->_token_file, $data->access_token);
    }

    function setJsticket()
    {
        $ticketurl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=" . $this->_access_token;
        $data = $this->returnWeChatJsonData($ticketurl);
        if($data->errcode > 0){
            $this->setToken();
            $this->setJsticket();
        }

        $this->_jsapi_ticket = $data->ticket;
        file_put_contents($this->_jsapi_ticket_file, $data->ticket);
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->_jsapi_ticket;

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->_appID,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }


    //返回构造好的跳转链接
    function getCodeRedirect($redirecturi, $state)
    {
        //https://open.weixin.qq.com/connect/qrconnect?
        //https://open.weixin.qq.com/connect/oauth2/authorize?
        $uri = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->_appID . "&redirect_uri=" . urlencode($redirecturi) . "&response_type=code&scope=snsapi_userinfo&state=" . $state . "#wechat_redirect";

        return $uri;
    }

    /**
     * access_token    接口调用凭证
     * expires_in    access_token接口调用凭证超时时间，单位（秒）
     * refresh_token    用户刷新access_token
     * openid    授权用户唯一标识
     * scope    用户授权的作用域，使用逗号（,）分隔
     * unionid    当且仅当该网站应用已获得该用户的userinfo授权时，才会出现该字段。
     *
     */
    function getTokenData($code)
    {
        $uri = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->_appID . "&secret=" . $this->_appsecret . "&code=" . $code . "&grant_type=authorization_code";
        $data = $this->returnWeChatJsonData($uri);
        return $data;
    }

    /**
     * 获取用户信息
     *  openid    普通用户的标识，对当前开发者帐号唯一
     * nickname    普通用户昵称
     * sex    普通用户性别，1为男性，2为女性
     * province    普通用户个人资料填写的省份
     * city    普通用户个人资料填写的城市
     * country    国家，如中国为CN
     * headimgurl    用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空
     * privilege    用户特权信息，json数组，如微信沃卡用户为（chinaunicom）
     * unionid    用户统一标识。针对一个微信开放平台帐号下的应用，同一用户的unionid是唯一的。
     */
    function getUserInfo($token)
    {
        $uri = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $token . "&openid=" . $this->_appID;
        $userInfo = $this->returnWeChatJsonData($uri);
        return $userInfo;
    }

    /**
     * 检验授权凭证（access_token）是否有效
     * 正确的Json返回结果：
     * {
     * "errcode":0,"errmsg":"ok"
     * }
     * 错误的Json返回示例:
     * {
     * "errcode":40003,"errmsg":"invalid openid"
     * }
     */
    function validateToken($token)
    {
        $uri = "https://api.weixin.qq.com/sns/auth?access_token=" . $token . "&openid=" . $this->_appID;
        $res = $this->returnWeChatJsonData($uri);
        return $res;
    }

    /**刷新或续期access_token使用
     * access_token    接口调用凭证
     * expires_in    access_token接口调用凭证超时时间，单位（秒）
     * refresh_token    用户刷新access_token
     * openid    授权用户唯一标识
     * scope    用户授权的作用域，使用逗号（,）分隔
     */
    function refreshToken($refresh_token)
    {
        $uri = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=" . $this->_appID . "&grant_type=refresh_token&refresh_token=" . $refresh_token;
        $res = $this->returnWeChatJsonData($uri);
        return $res;
    }

    /**
     * 发送模板消息
     *
     */
    function templateSend($touser,$templateid,$url,$data){
        $uri="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->_access_token;
        $obj=array('touser'=>$touser,'template_id'=>$templateid,'url'=>$url,'data'=>$data);
        $o=json_encode($obj);
        $res=$this->sendJsonData($uri,$o);
        return $res;
    }

    //获取模板id
    function getTemplateId($templateCode){
        $tempkv=array('TM00186'=>'报名结果通知','TM00080'=>'课程开课通知','OPENTM213512088'=>'待办任务提醒');
        $temps=$this->getAllTemplate();
        foreach ($temps->template_list as $t){
            if($t->title == $tempkv[$templateCode]){
                $t->errcode='0';
                return $t;
            }
        }
        $uri="https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=".$this->_access_token;
        $obj=array('template_id_short'=>$templateCode);
        $o=json_encode($obj);
        $res=$this->sendJsonData($uri,$o,1);
        return $res;
    }

    //获取模板列表
    function getAllTemplate(){
        $uri="https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=".$this->_access_token;
        $res=$this->sendJsonData($uri);
        return $res;
    }


    //请求json数据
    function returnWeChatJsonData($url, $parm = array(), $post = 0)
    {
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, $post); //post提交方式
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parm);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $jsondata = curl_exec($ch); //运行curl
        curl_close($ch);
        return json_decode($jsondata);
    }

    //发送json数据
    function sendJsonData($url, $parm = "", $post = 0)
    {
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, $post); //post提交方式
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parm);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json"));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $jsondata = curl_exec($ch); //运行curl
        curl_close($ch);
        return json_decode($jsondata);

    }

    //请求链接获取内容
    public function Get($url)
    {
        if (function_exists('file_get_contents')) {
            $file_contents = file_get_contents($url);
        } else {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }
        return $file_contents;
    }
}
