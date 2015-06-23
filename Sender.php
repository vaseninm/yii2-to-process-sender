<?php
namespace vaseninm\Yii2ToProcessSender;

use yii\base\Component;
use yii\base\Exception;

abstract class Sender extends Component {
    /**
     * @var array hash process => $config
     */
    public $processes = [];

    public function send($process, $route, $params = [])
    {
        if (array_key_exists($process, $this->processes)) {
            throw new Exception("Process '{$process}' not found in config");
        }

        return true;
    }
}