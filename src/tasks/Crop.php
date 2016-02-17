<?php
namespace Amirax\Imagico\tasks;

use Amirax\Imagico\TaskInterface;
use Imagine\Image\Point;
use Imagine\Image\Box;

class Crop implements TaskInterface {

    public static function apply(&$img, $params)
    {
        $size = $img->getSize();
        if(!isset($params['left'])) $params['left'] = 'center';
        if(!isset($params['top'])) $params['top'] = 'center';
        if(!isset($params['width']) || $params['width'] < 0) $params['width'] = $size->getWidth();
        if(!isset($params['height']) || $params['height'] < 0) $params['height'] = $size->getHeight();
        if($params['left'] < 0) $params['left'] = $size->getWidth() - $params['width'] + $params['left'];
        if($params['top'] < 0) $params['top'] = $size->getHeight() - $params['height'] + $params['top'];

        $params['left'] = ($params['left'] === 'center' && $size->getWidth() > $params['width'])
            ? abs(round(($size->getWidth() / 2) - ($params['width'] / 2)))
            : intval($params['left']);
        $params['top'] = ($params['top'] === 'center' && $size->getHeight() > $params['height'])
            ? abs(round(($size->getHeight() / 2) - ($params['height'] / 2)))
            : intval($params['top']);

        $img->crop(new Point($params['left'], $params['top']), new Box($params['width'], $params['height']));
    }

}