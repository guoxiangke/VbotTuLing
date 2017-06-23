<?php

namespace Guoxiangke\VbotTuLing;

use Hanson\Vbot\Extension\AbstractMessageHandler;

use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Contact\Myself;
use Hanson\Vbot\Message\Card;
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
    	/** @var Friends $friends */
        $friends = vbot('friends');

        /** @var Groups $groups */
        $groups = vbot('groups');

        // 获取自己实例
        $myself = vbot('myself');
        //自动转发管理员@群名称发的消息给机器人，然后去掉@群名后转发到对应的群里。
        //TODO; 确定管理员标准按照昵称？
        //begin of 群管理
        foreach ($groups as $gid => $group) {
            //check must be 群主
            if( isset($group['IsOwner']) && !$group['IsOwner']) {
                continue;
            }elseif( !isset($group['ChatRoomOwner']) || $group['ChatRoomOwner'] !== $myself->username) {
                continue;
            }

            //////begin!!//////
            // vbot('console')->log($group['NickName'],'<pre>'.print_r($message,1));
            // vbot('console')->log($gid,$group['NickName']);
            if ($message['from']['NickName'] === $group['NickName']) {
                //处理文本消息！
                $content = $message['content'];
                if ($message['type'] === 'text') {
                    switch ($content) {
                        case '群规':
                            $content='xxx查看了群规则，棒棒哒👍';
                            Text::send($message['from']['UserName'], $rule);
                            break;
                        default:
                            //自己不回复自己！
                            // vbot('console')->log('group_change:', '<pre>'.print_r($message,1));
                            if($message['fromType'] !== 'Self')
                                 Text::send($message['from']['UserName'], static::reply($message['pure'], $message['from']['UserName']));
                            break;
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