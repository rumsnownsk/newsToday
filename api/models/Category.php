<?php

namespace api\models;


use Valitron\Validator;

class Category extends CommonModel
{
    protected $table = 'category';
    public $timestamps = false;

    protected $fillable = [
        'title', 'parent_id'
    ];

    public static $rules = [
        'required' => ['title'],
        'integer' => ['parent_id'],
        'mustBeDiff' => [
            ['parent_id']
        ],
        'mustBe' => [
            ['parent_id']
        ]
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        Validator::addRule('mustBeDiff', function ($field, $value, array $params, array $fields){
            if ($fields[$field] == $this->id){
                return false;
            }
            return true;
        }, 'должно отличаться от своего id ');
        Validator::addRule('mustBe', function ($field, $value, array $params, array $fields){
            if (!Category::where('id', $value)->first()){
                return false;
            }
            return true;
        }, 'не существует ');
    }

    public static function add($fields)
    {
        $obj = new static();
        $obj->fill($fields);
        $obj->save();
        return $obj;
    }

    public function edit($fields){
        $this->fill($fields);
        $this->save();
        return $this;
    }

    public function remove(){
        try {
            return $this->delete();
        } catch (\Exception $e) {
            $this->errors = $e->getMessage();
            return false;
        }
    }
}