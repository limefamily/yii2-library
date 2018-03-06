<?php
namespace limefamily\library;

use Yii;
use yii\base\NotSupportedException;
use yii\web\ServerErrorHttpException;
use yii\base\Model;
use yii\web\IdentityInterface;

/**
 *
 * @property integer $id
 * @property string $employee_code
 * @property string $create_date
 * @property string $update_date
 * @property integer $org_id
 * @property string $org_code
 * @property string $org_name
 * @property string $true_name
 * @property string $nick_name
 * @property string $login_code
 * @property integer $sex
 * @property string $Living_place
 * @property string $birthday
 * @property string $order_num
 * @property integer $create_user_id
 * @property integer $last_modified_user_id
 * @property string $remark
 * @property string $password
 * @property string $salt
 * @property integer $status
 * @property string $phone
 */
class User extends Model implements IdentityInterface
{

    public  $id;
    public  $employee_code;
    public  $create_date;
    public  $update_date;
    public  $org_id;
    public  $org_code;
    public  $org_name;
    public  $true_name;
    public  $nick_name;
    public  $login_code;
    public  $sex;
    public  $Living_place;
    public  $birthday;
    public  $order_num;
    public  $create_user_id;
    public  $last_modified_user_id;
    public  $remark;
    public  $password;
    public  $salt;
    public  $status;
    public  $phone;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_date', 'update_date', 'birthday'], 'safe'],
            [['org_id', 'sex', 'create_user_id', 'last_modified_user_id', 'status'], 'integer'],
            [['true_name', 'login_code'], 'required'],
            [['order_num'], 'number'],
            [['employee_code', 'password', 'salt'], 'string', 'max' => 50],
            [['org_code'], 'string', 'max' => 48],
            [['org_name', 'remark'], 'string', 'max' => 255],
            [['true_name', 'nick_name', 'login_code'], 'string', 'max' => 32],
            [['Living_place'], 'string', 'max' => 128],
            [['phone'], 'string', 'max' => 11]
        ];
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return Yii::$app->limeRights->findIdentity($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return Yii::$app->limeRights->findIdentityByAccessToken($token,$type);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     * @throws ServerErrorHttpException
     */
    public static function findByUsername($username)
    {
        return Yii::$app->limeRights->findIdentityByUsername($username);
    }
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->limeRights->validatePassword($this->login_code,$password);
    }
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     * @throws NotSupportedException
     */
    public static function findByPasswordResetToken($token)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        throw new NotSupportedException('"getAuthKey" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException('"validateAuthKey" is not implemented.');
    }


    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
}
