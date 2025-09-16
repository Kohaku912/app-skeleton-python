<?php
namespace OCA\EarthquakeNotify\Service;

class TalkService {
    private $broker;

    public function __construct($broker = null) {
        $this->broker = $broker;
    }

    /**
     * roomToken にメッセージを投げる（内部で IBroker を使う or OCS API を叩く実装へ差し替え）
     */
    public function postMessageToRoom(string $roomToken, string $message) {
        if ($this->broker) {
            try {
                // IBroker を使って投稿する方法（環境によっては直接 HTTP OCS を使う方が簡単な場合あり）
                // 実際の IBroker の使い方は Nextcloud のバージョン/実装により差があります。
                // ここは「もし IBroker があるなら使う」ためのプレースホルダです。
                $this->broker->sendTextMessageToRoom($roomToken, $message);
                return true;
            } catch (\Throwable $e) {
                // フォールバック: OCS v2 API を curl 等で呼ぶ実装に切替える（省略）
            }
        }

        // フォールバック例: OCS API を curl で叩く（簡略）
        $base = \OC::$server->getURLGenerator()->getAbsoluteURL('/');
        $ocs = rtrim($base, '/') . '/ocs/v2.php/apps/spreed/api/v1/room/'.rawurlencode($roomToken).'/message';
        // 実運用：認証情報や OCS-APIRequest ヘッダを適切に付けて HTTP POST する
        // ここでは実装省略（参考: Talk API ドキュメント）。
        return false;
    }
}
