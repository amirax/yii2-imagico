<?php
namespace Amirax\Imagico\tasks;

use yii\base\Exception;
use Amirax\Imagico\TaskInterface;

class Rotate implements TaskInterface {

    public static function apply(&$img, $angle)
    {
        if(empty($angle)) throw new Exception('Parameter "angle" is required');
        $angle = self::_prepareAngle($angle);
        $img->rotate($angle);
    }


    private static function _prepareAngle($angle)
    {
        $angle = intval($angle);
        if ($angle > 180) {
            do {
                $angle -= 360;
            } while($angle > 180);
        } elseif($angle < -180) {
            do {
                $angle += 360;
            } while($angle < -180);
        }
        return $angle;
    }

}