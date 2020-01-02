<?php

namespace api\models;


use api\libs\ImageManager;
use Valitron\Validator;

class User extends CommonModel
{
    protected $table = 'user';

    protected $hidden = [
        'password'
    ];

    protected $fillable = [
        'fio', 'login', 'password', 'email',
    ];

    public static $rules = [
        'required' => [
            'fio', 'login', 'password'
        ],
        'email' => ['email']
    ];

    public $imageManager;

    public function __construct()
    {
        parent::__construct();
        $this->imageManager = new ImageManager('users');
    }

    /**
     * Создание нового юзера
     * @param array $fields - массив
     * @return object
     */
    public static function add($fields)
    {
        $user = new static();

        $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);

        $user->fill($fields);
        $user->save();
        return $user;
    }

    /**
     * Редактирование юзера
     * @param array $fields - массив
     * @return $this
     */
    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
        return $this;
    }

    /**
     * Связь с сущностью News
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function news(){
        return $this->hasMany(News::class, 'user_id', 'id');
    }

    /**
     * Удаление аватара Юзера
     * Удаление Юзера из БД
     * @return bool|null
     */
    public function remove()
    {
        try {
            $this->imageManager->delete(IMAGES.'/avatars/'.$this->avatar);
            return $this->delete();
        } catch (\Exception $e) {
            self::$errors = $e->getMessage();
            return false;
        }
    }


    public function setEmail($request)
    {
        if (isset($request['email']) && !empty($request['email'])) {
            $this->email = $request['email'];
            $this->save();
        }
    }

    public function setAvatar()
    {
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar'])) {
            $file = $_FILES['avatar'];

            if ($fileName = $this->imageManager->uploadTo($file, IMAGES.'/avatars/')) {
                $this->avatar = $fileName;
                return $this->save();

            } else {

                self::$errors = $this->imageManager->errors;
                return false;

            }
        }
        return null;
    }
}