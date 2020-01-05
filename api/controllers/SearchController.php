<?php


namespace api\controllers;

use api\models\Category;
use api\models\News;
use Illuminate\Database\Capsule\Manager as DB;

class SearchController extends CommonController
{
    // поиск новости по названию (или совпадению через оператор LIKE) (есть обязательные GET-параметры onfield и search)
    public function newsByLikeAction()
    {
        if (!isset($_GET['onfield']) || !isset($_GET['search'])){
            $this->response([
                'errors' => 'необходимы поля field и search'
            ], 422);
        };

        $onfield = $_GET['onfield'];
        $search = $_GET['search'];

        $news = News::query()->where($onfield, 'LIKE', "%{$search}%")->get();
        if ($news){
            $this->response($news, 200);
        } else {
            $this->response([], 204);
        }
    }

    // искать новости по рубрике, не включая дочерние (есть обязательные GET-параметры onfield и search)
    public function newsByCategoryAction(){
        if (!isset($_GET['search'])){
            $this->response([
                'errors' => 'необходимо поля search'
            ], 422);
        };

        $search = $_GET['search'];

        $news = DB::table('news')
            ->select('news.*')
            ->join('news_category', 'news.id', '=', 'news_category.news_id')
            ->join('category', 'news_category.category_id', '=', 'category.id')
            ->where('category.title', 'LIKE', "%{$search}%")
            ->groupBy('news.id')
            ->get();

        if ($news) {
            $this->response($news, 200);
        } else {
            $this->response([], 204);
        };
    }




}