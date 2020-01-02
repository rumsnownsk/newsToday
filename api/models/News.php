<?php
namespace api\models;

use api\libs\ImageManager;
use Illuminate\Database\Capsule\Manager as DB;
use Valitron\Validator;

class News extends CommonModel
{
    protected $table = 'news';
    public $imageManager;

    protected $fillable = [
        'title', 'user_id', 'text'
    ];

    public static $rules = [
        'required' => [
            'title', 'user_id', 'text', 'category_id'
        ],
        'integer' => ['user_id', 'tag_id'],
        'array' => ['category_id']
    ];

    public function __construct()
    {
        parent::__construct();
        $this->imageManager = new ImageManager('users');
    }

    /**
     * Создание новой записи
     * @param array $fields - массив
     * @return object
     */
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
            $this->removeCategories();

            return $this->delete();
        } catch (\Exception $e) {
            $this->errors = $e->getMessage();
            return false;
        }
    }

    public function setCategories(array $cat_ids){

        $this->removeCategories();

        foreach ($cat_ids as $cat_id) {
            DB::table('news_category')->updateOrInsert([
                'news_id' => $this->id,
                'category_id' => $cat_id
            ]);
        }
    }

    public function categories(){
        return $this->belongsToMany(Category::class, 'news_category');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function removeCategories(){
        DB::table('news_category')->where('news_id', $this->id)->delete();

    }
}