<?php

namespace api\controllers;

use api\models\News;

class NewsController extends CommonController
{
    public function indexAction()
    {
        $news = News::all();

        dd($_GET);
        if ($news) {
            $this->response($news, 200);
        }
        $this->response([], 204);
    }

    public function createAction()
    {
        if (News::validate(self::$requestParams, News::$rules)) {

            $news = News::add(self::$requestParams);

            $this->response($news, 201);
        } else {
            $this->response(News::getErrors(), 422);
        }
    }

    public function readAction($id)
    {
        $news = News::find($id);

        if (!$news) {
            $this->response([], 204);
        }
        $this->response($news, 200);

    }

    public function updateAction($id)
    {
        $news = News::find($id);

        if ($news) {
            if ($news->validate(self::$requestParams, News::$rules)) {

                $news = $news->edit(self::$requestParams);

                $this->response($news, 200);
            } else {
                $this->response([
                    'errors' => $news->getErrors()
                ], 422);
            }

        } else {
            $this->response([], 204);
        }
    }

    public function deleteAction($id)
    {
        if ($news = News::find($id)) {

            if ($news->remove()) {
                $this->response([
                ], 204);
            }
            $this->response([
                'errors' => $news->getErrors()
            ], 422);


        } else {
            $this->response([], 204);
        }
    }

    public function searchAction(){
        if (!$_GET['onfield'] || !$_GET['text']){
          $this->response([
              'errors' => 'необходимы поля field и search'
          ]);
        };

        $news = News::query()->where($_GET['onfield'], 'LIKE', "%{$_GET['text']}%")->get();
        $this->response($news, 200);
    }

}