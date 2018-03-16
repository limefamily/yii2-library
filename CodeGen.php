<?php
/**
 * Created by PhpStorm.
 * User: shelldoll
 * Date: 2018/2/5
 * Time: 15:43
 */

namespace limefamily\library;


use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\base\Component;
use yii;

class CodeGen extends Component
{
    //code前缀 驿站
    const CODE_STATION = '01';

    //code前缀 驻家地址
    const CODE_HOME = '02';

    //code前缀 楼/建筑物
    const CODE_BUILDING = '03';

    //code前缀 楼层
    const CODE_FLOOR = '04';

    //code前缀 房间
    const CODE_ROOM = '05';

    //code前缀 床位
    const CODE_BED = '06';
    
    /**
     * @param $id integer
     * @param $prefix integer
     * @return $code string
     */
    public  function createCode($id, $prefix)
    {
        $pad_len = 8;
        return $prefix . str_pad($id, $pad_len, 0, STR_PAD_LEFT);
    }
}
