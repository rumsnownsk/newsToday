<?php

namespace api\controllers;

use api\libs\ImageManager;
use Illuminate\Database\Capsule\Manager as DB;

class UploadController extends CommonController
{
    public $table;

    public $fieldInModel;
    public $pathToImage;


    // Название "картинковых" полей в моделях
    public static $fieldImage = [
        'user' => 'avatar',
        'news' => 'imageName',
    ];

    // Путь хранения картинок для моделей
    public static $pathsToImages = [
        'user' => IMAGES . '/avatars',
        'news' => IMAGES . '/news',
    ];

    public function fieldImage($nameModel)
    {
        if (!isset(self::$fieldImage[$nameModel])) {
            $this->response([
                'errors' => "Для модели " . ucfirst($nameModel) . " не предусмотрена картинка"
            ], 422);
        }
        $this->fieldInModel = self::$fieldImage[$nameModel];
    }

    public function pathToImage($nameModel)
    {
        if (!isset(self::$pathsToImages[$nameModel])) {
            $this->response([
                'errors' => "Для модели " . ucfirst($nameModel) . " не указана папка для сохранения картинок"
            ], 422);
        }
        $this->pathToImage = self::$pathsToImages[$nameModel];
    }


    public function uploadAction($nameModel, $id)
    {
        $this->fieldImage($nameModel);
        $this->pathToImage($nameModel);

        if (!isset($_FILES['file']) || empty($_FILES['file'])) {
            $this->response([
                'errors' => [
                    'File обязательное поле',
                    'File должно быть картинкой'
                ]
            ], 422);
        };

        if (count($_FILES) > 1) {
            $this->response([
                'errors' => 'Допускается только одна картинка'
            ], 422);
        }

        $imageManager = new ImageManager($this->pathToImage);

        if (!$imageManager->validate($_FILES['file'])) {
            $this->response(ImageManager::getErrors(), 422);
        }

        $object = DB::table($nameModel)->where('id', $id)->first();

        if (!$object) {
            $this->response([
                'errors' => ucfirst($nameModel) . " с id=$id не существует"
            ], 422);
        }

        $imageManager->currentImage = $object->{$this->fieldInModel};

        if ($fileName = $imageManager->uploadTo($_FILES['file'])) {

            DB::table($nameModel)->where('id', $id)->update([
                $this->fieldInModel => $fileName
            ]);

            $this->response([
                'path' => $this->pathToImage.'/'.$fileName,
                'message' => 'Картинка была успешно загружена на сервер'
            ], 200);
        } else {
            $this->response(ImageManager::getErrors(), 422);
        };
    }

    // Удаление непринадлежащих никому файлов
//    public function clearImagesAction($namePath)
//    {
//        $res = DB::table($namePath)->get()->map(function ($item, $key) use ($namePath){
//            return $item->{self::$fieldImage[$namePath]};
//        })->toArray();
//
//        $files = scandir(self::$pathsToImages[$namePath]);
//
//        foreach ($files as $file) {
//            if ($file == "." || $file == "..") {
//                continue;
//            }
//            if (in_array($file, $res)) {
//                continue;
//            } else {
//                unlink(self::$pathsToImages[$namePath] .'/'. $file);
//            };
//
//        }
//    }

}