<?php
namespace OCA\EarthquakeNotify\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ApiController extends Controller {
    private $earthquakeService;

    public function __construct($appName, IRequest $request, $earthquakeService) {
        parent::__construct($appName, $request);
        $this->earthquakeService = $earthquakeService;
    }

    /**
     * POST /apps/earthquake_notify/webhook
     * Accepts earthquake payload (JSON). Basic secret header check.
     */
    public function webhook() {
        $request = $this->request;
        $body = $request->getBody();
        $data = json_decode($body, true);
        if (!$data) {
            return new DataResponse(['status'=>'error','message'=>'invalid json'], 400);
        }

        // simple secret check (admin must set app config key 'webhook_secret')
        $config = \OC::$server->getIConfig();
        $secret = $config->getAppValue('earthquake_notify', 'webhook_secret', '');
        $provided = $request->getHeader('X-EQ-SECRET') ?? '';
        if ($secret !== '' && $provided !== $secret) {
            return new DataResponse(['status'=>'error','message'=>'unauthorized'], 401);
        }

        // delegate to service (非同期ジョブに回すことも可能だが、まずは同期処理)
        try {
            $this->earthquakeService->handleEvent($data);
        } catch (\Exception $e) {
            return new DataResponse(['status'=>'error','message'=>$e->getMessage()], 500);
        }

        return new DataResponse(['status'=>'ok']);
    }

    public function health() {
        return new DataResponse(['status'=>'ok','time'=>time()]);
    }
}
