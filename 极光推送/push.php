<?php

namespace app\common\logic;

use think\Db;
require_once './vendor/jpush/jpush/autoload.php';

class PushLogic
{
    private $jpush = null;
    
    function __construct() 
    {
        $c = Db::name('config')->where('name', 'IN', 'jpush_app_key,jpush_master_secret')->column('name,value');
        if ($c['jpush_app_key'] &&  $c['jpush_master_secret']) {
            $this->jpush = new \JPush\Client($c['jpush_app_key'], $c['jpush_master_secret']);
        }else{
            return ['status' => 1, 'msg' => '请配置推送服务相关设置！！'];
        }
    }

    /**
     * 推送消息,默认全部消息 用最新php sdk
     * title:标题，默认TPshop
     * @param $msg_content | 消息内容
     * @param array $data |发送的数据自定义 $required_keys = array('title', 'content_type', 'extras'); $data['title']
     * @param int $all | 1向所有用户发送，0,向指定用户发送
     * @param array $push_ids | 推送id
     * @return array
     */
    public function push($msg_content,$data=[], $all = 1, $push_ids = [])
    {
        if ($push_ids && is_array($push_ids)) {
            foreach ($push_ids as $k => $p) {
                if (empty($p)) {
                    unset($push_ids[$k]);
                }
            }
            if (!$push_ids) {
                return ['status' => -1, 'msg' => '个体推送时没有指定用户！'];
            }
        }
        
        if (!$this->jpush) {
            return ['status' => -1, 'msg' => '推送服务配置有误！'];
        } elseif (!$all && !$push_ids) {
            return ['status' => -1, 'msg' => '个体推送时没有指定用户！'];
        }
        if(!is_array($data) || empty($data['title'])){
            $data['title'] = 'TPshop';
        }
        $push = $this->jpush->push()
                ->setPlatform('all')
                ->setNotificationAlert($msg_content)
                ->message($msg_content,$data); // 改为加个标题
        if ($all) {
            $push = $push->addAllAudience();
        } else {
            $push = $push->addRegistrationId($push_ids);
        }
        
        try {
            $response = $push->send();
            if ($response['http_code'] != 200) {
                return ['status' => -1, 'msg' => "http错误码:{$response['http_code']}", 'result' => $response];
            }
            return ['status' => 1, 'msg' => '已推送', 'result' => $response];
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            return ['status' => -1, 'msg' => $e->getMessage()];
        } catch (\JPush\Exceptions\APIRequestException $e) {
            return ['status' => -1, 'msg' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['status' => -1, 'msg' => $e->getMessage()];
        }
    }
}