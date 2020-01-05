<?php
/**
 * Created by PhpStorm.
 * User: rum
 * Date: 05.01.20
 * Time: 14:40
 */

namespace api\controllers;
use api\models\Category;
use api\models\News;
use api\models\User;
use Faker\Factory;
use Illuminate\Database\Capsule\Manager as DB;

class FakerController
{
    public $faker;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
        header("Content-Type: application/json;  charset=UTF-8");
        header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Access-Control-Allow-Methods, Authorization, X-Requested-With, content-type");
        $this->faker = Factory::create('ru_RU');
    }

    public function runFakeGenerateAction(){

        $this->generateUsersAction();
        $this->generateCategoryAction();
        $this->generateNewsAction();
        $this->generateNewsCategoryAction();

        $this->response([
            'message' => "that's OK"
        ], 201);
    }

    private function generateUsersAction(){
        for ($i = 0; $i < 100; $i++){
            $user = new User();
            $user->fio = $this->faker->name;
            $user->login = $this->faker->userName;
            $user->password = $this->faker->sha256;
            $user->email = $this->faker->email;

            if (!$user->save()){
                $this->response($user, 422);
            };
        }
    }

    private function generateCategoryAction(){
        for ($i = 0; $i < 40; $i++){
            $model = new Category();
            $model->parent_id = $this->faker->numberBetween(0,20);
            $model->title = $this->faker->word;

            if (!$model->save()){
                $this->response($model, 422);
            };
        }
    }

    private function generateNewsAction(){
        for ($i = 0; $i < 5000; $i++){
            $model = new News();
            $model->user_id = $this->faker->numberBetween(1,10);
            $model->publish = $this->faker->numberBetween(0, 1);

            $text = $this->faker->realText(300);

            $model->text = $text;

            $shortArr = array_slice(explode(" ", $text), 0, 4);

            $model->title = implode(" ", $shortArr);

            if (!$model->save()){
                $this->response($model, 422);
            };
        }
    }

    private function generateNewsCategoryAction(){
        for ($i = 0; $i < 100; $i++){
            DB::table('news_category')->insert([
                'news_id' => $this->faker->numberBetween(1, News::all()->count()),
                'category_id' => $this->faker->numberBetween(1, Category::all()->count())
            ]);
        }
    }


    protected function response($data = [], $status = 500)
    {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    private function requestStatus($code)
    {
        $status = array(
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])
            ? $status[$code]
            : $status[500];
    }

}