<?php
namespace api\models;

use api\libs\ImageManager;
use Valitron\Validator;

class News extends CommonModel
{
    protected $table = 'news';
    public $imageManager;

    protected $fillable = [
        'title', 'user_id', 'text', 'category_id', 'tag_id'
    ];

    public static $rules = [
        'required' => [
            'title', 'user_id', 'text', 'category_id'
        ],
        'integer' => ['user_id', 'category_id', 'tag_id']
    ];

    public function __construct()
    {
        parent::__construct();
        $this->imageManager = new ImageManager('users');
    }

    public static function add($fields){
        $news = new static();
        $news->fill($fields);
        $news->save();
        return $news;
    }

    public function edit($fields){
        $this->fill($fields);
        $this->save();
        return $this;
    }

    public function remove(){
        try {
            $this->imageManager->delete(IMAGES.'/news/'.$this->imageName);
            return $this->delete();
        } catch (\Exception $e) {
            $this->errors = $e->getMessage();
            return false;
        }
    }

}