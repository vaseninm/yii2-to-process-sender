<?php
namespace vaseninm\Yii2ToProcessSender\redis;

use Codeception\Exception\ConfigurationException;
use vaseninm\Yii2ToProcessSender\Sender as AbstractSender;
use yii\base\Component;
use yii\helpers\Json;
use yii\redis\Connection;

class Sender extends AbstractSender {

    const PREFIX = 'php_message_';
    const DEFAULT_PROCESS = 'main';

    private $_connection = [];

    public function send($route, $params = [], $process = self::DEFAULT_PROCESS)
    {
        $connection = $this->getConnection($process);

        $connection->executeCommand('publish', [
            self::PREFIX . $process,
            Json::encode([
                'route' => $route,
                'params' => $params,
            ])
        ]);

        return true;
    }

    /**
     * @param $process
     * @return Connection
     * @throws ConfigurationException
     * @throws \yii\base\InvalidConfigException
     */
    private function getConnection($process)
    {
        if (! array_key_exists($process, $this->processes)) throw new ConfigurationException("Process $process not config in 'to process' components config");

        if (! array_key_exists($process, $this->_connection)) {

            if (isset($this->processes[$process])) {
                $this->_connection[$process] = \Yii::$app->get($this->processes[$process]);
            } else {
                $class = $this->processes[$process]['class'];
                $this->_connection[$process] = new $class($this->processes[$process]);
            }
        }

        return $this->_connection[$process];
    }
}