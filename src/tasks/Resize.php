<?php
namespace Amirax\Imagico\tasks;

use Amirax\Imagico\TaskInterface;
use Imagine\Image\Point;
use Imagine\Image\Box;

class Resize implements TaskInterface {

    const RESIZE_PRECISE   = 'precise';
    const RESIZE_WIDEN     = 'widen';
    const RESIZE_HEIGHTEN  = 'heighten';
    const RESIZE_CROP      = 'crop';
    const RESIZE_ADAPTIVE  = 'adaptive';

    const FILTER_UNDEFINED = 'undefined';
    const FILTER_POINT     = 'point';
    const FILTER_BOX       = 'box';
    const FILTER_TRIANGLE  = 'triangle';
    const FILTER_HERMITE   = 'hermite';
    const FILTER_HANNING   = 'hanning';
    const FILTER_HAMMING   = 'hamming';
    const FILTER_BLACKMAN  = 'blackman';
    const FILTER_GAUSSIAN  = 'gaussian';
    const FILTER_QUADRATIC = 'quadratic';
    const FILTER_CUBIC     = 'cubic';
    const FILTER_CATROM    = 'catrom';
    const FILTER_MITCHELL  = 'mitchell';
    const FILTER_LANCZOS   = 'lanczos';
    const FILTER_BESSEL    = 'bessel';
    const FILTER_SINC      = 'sinc';


    public static function apply(&$img, $params)
    {
        $size = $img->getSize();
        $mode = (!empty($params['mode'])) ? $params['mode'] : self::RESIZE_PRECISE;
        if(empty($params['filter'])) $params['filter'] = self::FILTER_UNDEFINED;
        if(!empty($params['constrainOnly'])) {
            if(!empty($params['width']) && $size->getWidth() < $params['width']) $params['width'] = $size->getWidth();
            if(!empty($params['height']) && $size->getHeight() < $params['height']) $params['height'] = $size->getHeight();
        }

        $methodName = '_mode' . ucfirst($mode);
        if(method_exists(__CLASS__, $methodName)) self::$methodName($img, $params);
    }


    protected static function _modePrecise(&$img, $params)
    {
        $img->resize(new Box($params['width'], $params['height']), $params['filter']);
    }


    protected static function _modeWiden(&$img, $params)
    {
        $size = $img->getSize();
        if($params['width'] > $size->getWidth()) $params['width'] = $size->getWidth();
        $newSize = $size->widen($params['width']);
        $img->resize(new Box($newSize->getWidth(), $newSize->getHeight()), $params['filter']);
    }


    protected static function _modeHeighten(&$img, $params)
    {
        $size = $img->getSize();
        if($params['height'] > $size->getHeight()) $params['height'] = $size->getHeight();
        $newSize = $size->heighten($params['height']);
        $img->resize(new Box($newSize->getWidth(), $newSize->getHeight()), $params['filter']);
    }


    protected static function _modeCrop(&$img, $params)
    {
        $size = $img->getSize();
        $ratio = $size->getWidth() / $size->getHeight();
        $newSize = ($ratio > 1) ? $size->heighten($params['height']) : $size->widen($params['width']);
        $img->resize(new Box($newSize->getWidth(), $newSize->getHeight()), $params['filter']);

        Crop::apply($img, $params);
    }


    protected static function _modeAdaptive(&$img, $params)
    {
        $size = $img->getSize();
        $ratio = $size->getWidth() / $size->getHeight();
        $newSize = ($ratio > 1) ? $size->widen($params['width']) : $size->heighten($params['height']);
        $img->resize(new Box($newSize->getWidth(), $newSize->getHeight()), $params['filter']);
    }

}