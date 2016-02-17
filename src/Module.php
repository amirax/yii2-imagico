<?php
namespace Amirax\Imagico;

use Yii;
use yii\base\Component;

/**
 * Amirax Imagico
 *
 * @author Max Voronov <maxivoronov@gmail.com>
 * @link http://www.amirax.ru/
 * @link https://github.com/amirax/yii2-imagico
 * @license https://github.com/amirax/yii2-imagico/blob/master/LICENSE.md
 * @date 07.02.2016
 */
class Module extends Component {

    /**
     * @var string Driver: GD or ImageMagick
     */
    public $engine;

    /**
     * @var string Path to cache directory
     */
    public $cacheDir;

    /**
     * @var string URL to cache directory
     */
    public $cacheUrl;


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->cacheDir = Yii::getAlias($this->cacheDir);
        $this->cacheUrl = Yii::getAlias($this->cacheUrl);
    }


    /**
     * @param string $file
     * @return Image
     */
    public function load($file)
    {
        return new Image($file, $this);
    }

}