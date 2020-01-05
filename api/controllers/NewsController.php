<?php

namespace api\controllers;

use api\models\News;

class NewsController extends CommonController
{
    public function indexAction()
    {
        $news = News::all();

        if ($news) {
            $this->response($news, 200);
        }
        $this->response([], 204);
    }

    public function createAction()
    {
        if (News::validate(self::$requestParams, News::$rules)) {

            $news = News::add(self::$requestParams);
            $news->setCategories(self::$requestParams['category_id']);

            $news['categories'] = $news->categories;

            $this->response($news, 201);
        } else {
            $this->response(News::getErrors(), 422);
        }
    }

    public function readAction($id)
    {
        if ($news = News::find($id)) {
            $news->user;
            $news->categories;

            $this->response($news, 200);
        }
        $this->response([], 204);

    }

    public function updateAction($id)
    {
        if ($news = News::find($id)) {

            if ($news->validate(self::$requestParams, News::$rules)) {

                $news = $news->edit(self::$requestParams);
                $news->setCategories(self::$requestParams['category_id']);
                $news['categories'] = $news->categories;

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

}