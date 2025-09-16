<?php
/**
 * 管理者用設定テンプレート（簡易）
 * ルーティングで admin settings を呼び出す実装に合わせて表示します。
 */
?>
<div class="section">
  <h2>Earthquake Notify — 管理設定</h2>

  <form id="earthquake-admin-form" method="post" action="#">
    <label>Webhook secret (optional)</label>
    <input name="webhook_secret" value="<?php p($_['webhook_secret']) ?>">

    <label>Minimum magnitude to notify</label>
    <input name="min_magnitude" type="number" step="0.1" value="<?php p($_['min_magnitude']) ?>">

    <label>Talk room token</label>
    <input name="talk_room_token" value="<?php p($_['talk_room_token']) ?>">

    <button type="submit">保存</button>
  </form>
</div>
