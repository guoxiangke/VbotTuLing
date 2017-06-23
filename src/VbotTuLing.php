<?php

namespace Guoxiangke\VbotTuLing;

use Hanson\Vbot\Extension\AbstractMessageHandler;

use Hanson\Vbot\Contact\Groups;
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
        //TODO 第一次需要@我
        //TODO 如果其他返回消息了，不用机器人！
        foreach ($groups as $gid => $group) {
            //////begin!!//////
            if ($message['type'] === 'text') {
                $keywords_ingroup = ['群规','关注','名片'];
                if(!in_array($message['content'], $keywords_ingroup)){
                    if($message['fromType'] !== 'Self' //自己不回复自己！
                        && (//不是自己的群，不回复！
                            (isset($group['IsOwner']) && $group['IsOwner'])
                            || (isset($message['from']['IsOwner']) && $message['from']['IsOwner'])
                            )
                        ){
                        // if($message['isAt']) {
                        //     //不是@我不回！
                        // }
                        //Extension on/info 不要回复！
                        $pattern ='/ (on|off|info)$/';
                        if(!preg_match($pattern, $message['content'])){
                            Text::send($message['from']['UserName'], static::reply($message['pure'], $message['from']['UserName']));
                            return;
                        }
                    }
                }
            }
            //////end!!//////
        }
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