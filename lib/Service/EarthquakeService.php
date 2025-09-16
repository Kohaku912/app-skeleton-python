<?php
namespace OCA\EarthquakeNotify\Service;

use OCP\IConfig;
use OCP\IUserManager;
use OCP\Notification\IManager as INotificationManager;

class EarthquakeService {
    private $config;
    private $userManager;
    private $notificationManager;
    private $talkService;

    public function __construct(IConfig $config, IUserManager $userManager, INotificationManager $notificationManager, $talkBroker = null) {
        $this->config = $config;
        $this->userManager = $userManager;
        $this->notificationManager = $notificationManager;
        $this->talkService = new TalkService($talkBroker);
    }

    /**
     * Webhook で受け取った地震データを処理
     * $event は配列（例: ['magnitude'=>5.4,'place'=>'Tokyo','time'=>'2025-09-16T11:00:00Z', ...]）
     */
    public function handleEvent(array $event) {
        // 1) 簡易フィルタ（閾値） — 管理設定から取得
        $minMag = (float)$this->config->getAppValue('earthquake_notify','min_magnitude','0');
        if (isset($event['magnitude']) && $event['magnitude'] < $minMag) {
            return; // 無視
        }

        // 2) 全ユーザーに Notification を作成
        foreach ($this->userManager->search('', 0, PHP_INT_MAX) as $user) {
            $uid = $user->getUID();

            // ユーザーごとの通知設定を確認（簡易例）
            $enabled = $this->config->getUserValue($uid, 'earthquake_notify', 'enabled', '1');
            if ($enabled !== '1') {
                continue;
            }

            // Notification 作成（NotificationManager 経由）
            $notification = new \OCP\Notification\Notification();
            $notification->setObject('earthquake_notify', (string)($event['time'] ?? time()));
            $notification->setApp('earthquake_notify');
            $notification->setSubject($event['place'] . ' M' . ($event['magnitude'] ?? '?'));
            $notification->setMessage('Earthquake detected: ' . ($event['detail'] ?? 'See details'));
            $notification->setUser($uid);
            $this->notificationManager->notify($notification);
        }

        // 3) Talk に投稿（必要であればルーム作成→投稿）
        $roomToken = $this->config->getAppValue('earthquake_notify','talk_room_token','');
        if (!empty($roomToken)) {
            $text = sprintf("Earthquake alert: %s M%s at %s", $event['place'] ?? '-', $event['magnitude'] ?? '-', $event['time'] ?? '-');
            $this->talkService->postMessageToRoom($roomToken, $text);
        }

        // 4) DB に記録や管理用ログ（ここは省略。実運用では専用テーブルに保存）
    }

    public function getStats() {
        // TODO: 実運用なら DB から集計する。ここではダミー値
        return [
            'drills' => 3,
            'last_event' => date('c'),
            'notified_users' => 123
        ];
    }

    public function setUserStatus($userId, $status) {
        // 簡易：ユーザーごとの appconfig に状態を保存
        $this->config->setUserValue($userId, 'earthquake_notify', 'safety_status', $status);
    }
}
