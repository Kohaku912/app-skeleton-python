<?php
namespace OCA\EarthquakeNotify\AppInfo;

use OCP\AppFramework\App;
use OCP\IDBConnection;
use OCP\IConfig;

class Application extends App {
    public function __construct(array $urlParams = []) {
        parent::__construct('earthquake_notify', $urlParams);
        $container = $this->getContainer();

        // サービス登録（シンプル実装：DIコンテナに登録）
        $container->registerService('EarthquakeService', function($c) {
            $server = \OC::$server;
            return new \OCA\EarthquakeNotify\Service\EarthquakeService(
                $server->getIConfig(),
                $server->getUserManager(),
                $server->getNotificationManager(),
                $server->query('OCP\Talk\IBroker') // Talk broker（存在すれば）
            );
        });

        $container->registerService('TalkService', function($c) {
            $server = \OC::$server;
            return new \OCA\EarthquakeNotify\Service\TalkService(
                $server->query('OCP\Talk\IBroker')
            );
        });
    }
}
