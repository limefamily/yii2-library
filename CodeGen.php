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

    private $db;
    public $useDefaultDb;
    public $dsn;
    public $username;
    public $password;
    public $charset;


    public function init(){
        parent::init();

        if ($this->useDefaultDb){
            $this->db = Yii::$app->db;
        }else{
            if ($this->dsn === null) {
                throw new InvalidConfigException('CodeGen::dsn must be set.');
            }
            if ($this->username === null) {
                throw new InvalidConfigException('CodeGen::username must be set.');
            }
            if ($this->password === null) {
                throw new InvalidConfigException('CodeGen::password must be set.');
            }
            $this->db = new yii\db\Connection([
                'dsn' => $this->dsn,
                'username' => $this->username,
                'password' => $this->password,
                'charset' => $this->charset,
            ]);
        }
    }
    /**
     * @param $id integer
     * @param $prefix integer
     * @return $code string
     * @throws ErrorException
     */
    public  function createCode($id, $prefix)
    {
        $pad_len = 8;
        return $prefix . str_pad($id, $pad_len, 0, STR_PAD_LEFT);
    }
}
