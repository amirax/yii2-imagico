<?php
namespace Amirax\Imagico\tasks;

use Amirax\Imagico\TaskInterface;

class Flip implements TaskInterface {

    const FLIP_HORIZONTALLY = 0;
    const FLIP_VERTICALLY   = 1;

    public static function apply(&$img, $mode)
    {
        if($mode == self::FLIP_VERTICALLY) {
            $img->flipVertically();
        } else {
            $img->flipHorizontally();
        }
    }

}