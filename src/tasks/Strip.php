<?php
namespace Amirax\Imagico\tasks;

use Amirax\Imagico\TaskInterface;

class Strip implements TaskInterface {

    public static function apply(&$img, $params)
    {
        $img->strip();
    }

}