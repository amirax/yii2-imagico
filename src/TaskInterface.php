<?php
namespace Amirax\Imagico;

interface TaskInterface {

    /**
     * @param \Imagine\Imagick\Image|\Imagine\Gd\Image $img
     * @param mixed $params
     */
    public static function apply(&$img, $params);

}