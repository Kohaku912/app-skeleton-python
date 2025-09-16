<?php
// appinfo/app.php
// アプリ初期化ファイル（安全な include ガード付き）
// このファイルはクラス重複読み込みによる "Cannot declare class ..." を防ぐ

if (!\class_exists(\OCA\EarthquakeNotify\AppInfo\Application::class)) {
    // ファイルパスは環境に応じて正しいことを確認
    require_once __DIR__ . '/../lib/AppInfo/Application.php';
}

// Application のインスタンスを返す（Nextcloud はこれを期待）
return new \OCA\EarthquakeNotify\AppInfo\Application();
