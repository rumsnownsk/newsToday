<?php
/**
 * Created by PhpStorm.
 * User: rum
 * Date: 05.01.20
 * Time: 14:40
 */

namespace api\controllers;


class FakerController
{
    public $faker;

    public function __construct()
    {
        $this->faker = Factory::create('ru_RU');
    }

}