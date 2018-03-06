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
    //组织机构code
    const ORG_CODE = 'o_code';

    //实体code 站点
    const ENTITY_CODE = 'e_code';

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
     * @param $type integer
     * @param $prefix integer
     * @return $code string
     * @throws ErrorException
     */
    public  function createCode($type, $prefix)
    {
        if ($type == self::ORG_CODE) {
            $pad_len = 8;
        } else if ($type == self::ENTITY_CODE) {
            $pad_len = 12;
        } else {
            throw new ErrorException('参数错误');
        }

        $cmd = $this->db->createCommand("call max_id(:reg,@s)");
        $cmd->bindParam(':reg', $type, \PDO::PARAM_STR, 10);
        $cmd->execute();
        $s = $this->db->createCommand("select @s");
        $ret = $s->queryOne();
        if (!empty($ret) && !empty($ret['@s'])) {
            return $prefix . str_pad($ret['@s'], $pad_len, 0, STR_PAD_LEFT);
        } else {
            throw new ErrorException('生成编号错误');
        }
    }
}