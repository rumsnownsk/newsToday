<?php
/**
 * Created by PhpStorm.
 * User: rum
 * Date: 24.12.19
 * Time: 19:59
 */

namespace api\models;

use api\libs\HelpersTrait;
use Illuminate\Database\Eloquent\Model;
use Valitron\Validator;

abstract class CommonModel extends Model
{
    use HelpersTrait;

    public static $errors = array();
    public static $rules = array();
    protected $hidden = array('pivot');


    public static function validate($data, $rules)
    {
        Validator::langDir(ROOT.'/vendor/vlucas/valitron/lang'); // always set langDir before lang.
        Validator::lang('ru');

        $v = new Validator($data);

        $v->rules($rules);

        if ($v->validate()) {
            return true;
        } else {
            self::$errors = array_merge($v->errors(), self::$errors);
            return false;
        }
    }
}