<?php
/**
 * Created by PhpStorm.
 * User: qingmeng-02
 * Date: 2018/2/1
 * Time: 17:29
 */

namespace limefamily\library;
use Yii;
use yii\rbac\CheckAccessInterface;

class LimeAccessChecker implements CheckAccessInterface
{
    public function checkAccess($userId, $permissionName, $params = [])
    {
        return Yii::$app->limeRights->checkAccess($userId, $permissionName, $params);
    }
}