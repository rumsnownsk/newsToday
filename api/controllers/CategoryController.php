<?php
/**
 * Created by PhpStorm.
 * User: rum
 * Date: 25.12.19
 * Time: 20:16
 */

namespace api\controllers;


use api\models\Category;
use api\models\News;

class CategoryController extends CommonController
{
    public function indexAction()
    {
        $categories = Category::all()->keyBy('id')->toArray();

        $tree = $this->buildTree($categories);
//        dd($tree);
        if ($tree) {
            $this->response($tree, 200);
        }
        $this->response([], 204);
    }


    public function buildTree(array &$elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['child'] = $children;
                }
                $branch[] = $element;
                unset($elements[$element['id']]);
            }
        }
        return $branch;
    }


    public function createAction()
    {
        $category = new Category();

        if ($category->validate(self::$requestParams, Category::$rules)) {

            $category = Category::add(self::$requestParams);

            $this->response($category, 201);

        } else {
            $this->response([
                'errors' => $category->getErrors()
            ], 422);
        }
    }

    public function readAction($id)
    {
        $category = Category::find($id)->toArray();

        if ($category) {
            $this->response($category, 200);
        } else {
            $this->response([], 204);
        }
    }

    public function updateAction($id)
    {
        $category = Category::find($id);

        if ($category) {
            if ($category->validate(self::$requestParams, Category::$rules)) {

                $category = $category->edit(self::$requestParams);

                $this->response($category, 200);
            } else {
                $this->response($category->getErrors(), 422);
            }
        } else {
            $this->response([], 204);
        }
    }

    public function deleteAction($id)
    {
        $category = Category::find($id);
        if ($category) {

            if ($category->remove()) {
                $this->response([
                    'message' => 'Data deleted'
                ], 204);
            }
            $this->response([
                'errors' => $category->getErrors()
            ], 422);


        } else {
            $this->response([], 204);
        }
    }

    public function newsCategoryAction($id)
    {
        if ($news = News::where('category_id', $id)->get()) {
            $this->response($news, 200);
        } else {
            $this->response([], 204);
        };
    }



}