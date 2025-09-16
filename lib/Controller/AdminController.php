<?php
namespace OCA\EarthquakeNotify\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class AdminController extends Controller {
    private $earthquakeService;

    public function __construct($appName, IRequest $request, $earthquakeService) {
        parent::__construct($appName, $request);
        $this->earthquakeService = $earthquakeService;
    }

    public function stats() {
        // サンプル：訓練回数や最新通知統計を返す（実装は EarthquakeService に委譲）
        $stats = $this->earthquakeService->getStats();
        return new DataResponse($stats);
    }

    public function setUserStatus($userId) {
        $body = json_decode($this->request->getBody(), true);
        $status = $body['status'] ?? null; // allowed: safe, unsafe, unavailable
        if (!$status) {
            return new DataResponse(['error'=>'missing status'], 400);
        }
        $this->earthquakeService->setUserStatus($userId, $status);
        return new DataResponse(['status'=>'ok']);
    }
}
