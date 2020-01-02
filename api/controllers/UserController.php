<?php
/**
 * Created by PhpStorm.
 * User: rum
 * Date: 25.12.19
 * Time: 21:38
 */

namespace api\controllers;

use api\models\News;
use api\models\User;

class UserController extends CommonController
{
    public function indexAction($news = null)
    {
        if ($user = User::all()) {
            $user = $user->each(function ($item){
                $user['news'] = $item->news;
            });

            $this->response($user, 200);
        }
        $this->response([], 204);
    }

    public function createAction()
    {
        if (User::validate(self::$requestParams, User::$rules)) {

            $user = User::add(self::$requestParams);
            $user->setEmail(self::$requestParams);

            $this->response($user, 201);
        } else {
            $this->response([
                'errors' => User::getErrors()
            ], 422);
        }
    }

    public function readAction($id)
    {
        if ($user = User::find($id)) {

            $user['news'] = $user->news;

            $this->response($user, 200);
        }
        $this->response([], 204);
    }

    public function updateAction($id)
    {
        if ($user = User::find($id)) {

            $rewriteRules = array_replace(User::$rules, [
                'required' => [
                    'fio', 'login'
                ]
            ]);

            if (User::validate(self::$requestParams, $rewriteRules)) {

                $user = $user->edit(self::$requestParams);
                $user['news'] = $user->news;

                $this->response($user, 200);
            } else {
                $this->response([
                    'errors' => User::getErrors()
                ], 422);
            }

//            $this->response($news, 200);
        } else {
            $this->response([], 204);
        }
    }

    public function deleteAction($id)
    {
        if ($user = User::find($id)) {

            if ($user->remove()) {
                $this->response([], 204);
            }
            $this->response([
                'errors' => $user->getErrors()
            ], 422);

        } else {
            $this->response([], 204);
        }
    }

    public function newsUserAction($id){
        $news = News::where('user_id', $id)->get()->toArray();
        $this->response($news, 200);
    }

}