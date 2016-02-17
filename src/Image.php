<?php
namespace Amirax\Imagico;

use yii\base\Object;
use yii\base\Exception;
use Imagine\Gd\Imagine AS GdEngine;
use Imagine\Imagick\Imagine AS ImagineEngine;

class Image extends Object {

    private $_imageObj;
    private $_settings;
    private $_tasks;


    /**
     * @inheritdoc
     * @param string $file
     * @param \Amirax\Imagico\Module $module
     */
    public function __construct($file, $module)
    {
        $this->_settings['sourcePath'] = $file;
        $this->setCacheDir($module->cacheDir);
        $this->setCacheUrl($module->cacheUrl);
        $this->setEngine($module->engine);
    }


    /**
     * @inheritdoc
     * @param string $method
     * @param array $args
     */
    public function __call($method, $args)
    {
        if(!property_exists($this, $method)) {
            if(!isset($args[0])) $args[0] = null;
            return $this->addTask($method, $args[0]);
        }
        return call_user_func_array(array($this, $method), $args);
    }


    /**
     * Add new task for processing
     *
     * @param string $type
     * @param array $params
     * @return $this
     * @throws Exception
     */
    public function addTask($type, $params = [])
    {
        $taskClass = __NAMESPACE__ . '\\tasks\\' . ucfirst($type);
        if(!class_exists($taskClass)) {
            throw new Exception('Task "' . $taskClass . '" not exists');
        }
        $this->_tasks[] = ['class' => $taskClass, 'type' => $type, 'params' => $params];
        return $this;
    }


    /**
     * Output processed image (with headers)
     *
     * @param string $format
     * @param array $options
     */
    public function render($format = 'jpg', array $options = [])
    {
        $this->processImage()
            ->show($format, $options);
    }


    /**
     * Save processed image
     *
     * @param $path
     * @param array $options
     */
    public function save($path, array $options = [])
    {
        $this->processImage()
            ->save($path, $options);
    }


    /**
     * Save processed image to cache and return image url
     *
     * @param array $options
     * @return string
     */
    public function saveToCache(array $options = [])
    {
        $sourcePathData = pathinfo($this->_settings['sourcePath']);
        $pathHash = md5(serialize($this->_tasks) . '|' . $sourcePathData['dirname']);
        $filePath = implode(DIRECTORY_SEPARATOR, [
            substr($pathHash, 0, 2),
            substr($pathHash, 2, 2),
            $pathHash,
            $sourcePathData['basename']
        ]);

        $path = $this->getCacheDir() . $filePath;
        if(!is_file($path)) {
            if(!is_dir(dirname($path))) mkdir(dirname($path), 0755, true);
            $this->save($path, $options);
        }
        return $this->getCacheUrl() . $filePath;
    }


    /**
     * Process image and return Imagine object
     *
     * @return \Imagine\Gd\Image|\Imagine\Imagick\Image
     */
    public function process()
    {
        return $this->processImage();
    }


    /**
     * Reset tasks and processed image
     *
     * @return $this
     */
    public function reset()
    {
        $this->_imageObj = null;
        $this->_tasks = [];
        return $this;
    }


    /**
     * Process image
     *
     * @return \Imagine\Imagick\Image | \Imagine\Gd\Image
     */
    protected function processImage()
    {
        $engineObj = ($this->getEngine() == 'imagick') ? new ImagineEngine : new GdEngine;
        $this->_imageObj = $engineObj->open($this->getSource());
        if(!empty($this->_tasks)) {
            foreach($this->_tasks AS $taskId=>$task) {
                /** @var $taskClass TaskInterface */
                $taskClass = $task['class'];
                $taskClass::apply($this->_imageObj, $task['params'], $engineObj);
                unset($this->_tasks[$taskId]);
            }
        }
        return $this->_imageObj;
    }


    /**
     * Set path to cache directory
     *
     * @param string $dirPath
     * @return $this
     */
    public function setCacheDir($dirPath)
    {
        $this->_settings['cacheDir'] = $dirPath;
        return $this;
    }


    /**
     * Get path to cache directory
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->_settings['cacheDir'];
    }


    /**
     * Set url to cache directory
     *
     * @param string $url
     * @return $this
     */
    public function setCacheUrl($url)
    {
        $this->_settings['cacheUrl'] = $url;
        return $this;
    }


    /**
     * Get url to cache directory
     *
     * @return string
     */
    public function getCacheUrl()
    {
        return $this->_settings['cacheUrl'];
    }


    /**
     * Set graphic engine (GD or ImageMagick)
     *
     * @param string $engine
     * @return $this
     */
    public function setEngine($engine)
    {
        $availableEngines = ['imagick', 'gd'];
        $engine = strtolower($engine);
        $this->_settings['engine'] = (!in_array($engine, $availableEngines))
            ? array_shift($availableEngines)
            : $engine;
        return $this;
    }


    /**
     * Get used graphic engine
     *
     * @return string
     */
    public function getEngine()
    {
        return $this->_settings['engine'];
    }


    /**
     * Get source image path
     *
     * @return string
     */
    public function getSource()
    {
        return $this->_settings['sourcePath'];
    }

}