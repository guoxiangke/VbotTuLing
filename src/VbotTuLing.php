<?php

namespace Guoxiangke\VbotTuLing;

use Hanson\Vbot\Extension\AbstractMessageHandler;

use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Contact\Myself;
use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;

class VbotTuLing extends AbstractMessageHandler
{

    public $author = 'Dale.Guo';

    public $version = '1.0';

    public $name = 'tuling';

    public $zhName = '图灵对话';

    private static $array = [];

    public function handler(Collection $message)
    {

        /** @var Groups $groups */
        $groups = vbot('groups');

        // 获取自己实例
        $myself = vbot('myself');
        //自动转发管理员@群名称发的消息给机器人，然后去掉@群名后转发到对应的群里。
        //TODO; 确定管理员标准按照昵称？
        //begin of 群管理
        foreach ($groups as $gid => $group) {
            // vbot('console')->log($gid,$group['NickName']);
            // vbot('console')->log($group['NickName'],'<pre>'.print_r($group,1));
            // vbot('console')->log($group['NickName'],'<pre>'.print_r($message,1));

            //////begin!!//////
            
             if ($message['from']['NickName'] === $group['NickName']) {
                //处理文本消息！//TODO 第一次需要@我
                if ($message['type'] === 'text') {
                    $keywords_ingroup = ['群规','关注','名片'];
                    if(!in_array($message['content'], $keywords_ingroup)){
                        if($message['fromType'] !== 'Self' && $message['from']['ChatRoomOwner']==$myself->username){
                            //不是自己的群，不回复！
                            //自己不回复自己！
                            // if($message['isAt']) //不是@我不回！
                            //Extension on/info 不要回复！
                            $pattern ='/ (on|off|info)$/';
                            if(!preg_match($pattern, $message['content']))
                                Text::send($message['from']['UserName'], static::reply($message['pure'], $message['from']['UserName']));
                        }
                    }
                }
                //other type with content!!!
            }
            //////end!!//////
        }//end of 群管理
    }

    private static function reply($content, $id)
    {
        try {
            $result = vbot('http')->post('http://www.tuling123.com/openapi/api', [
                'key'    => '88c9a1a8af8b4e6cb071a5033d81bc6c',
                'info'   => $content,
                'userid' => $id,
            ], true);

            return isset($result['url']) ? $result['text'].$result['url'] : $result['text'];
        } catch (\Exception $e) {
            return '图灵API连不上了，再问问试试';
        }
    }
    /**
     * 注册拓展时的操作.
     */
    public function register()
    {

    }
}