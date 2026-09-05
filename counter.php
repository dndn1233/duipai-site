<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
/* 机器人 UA：不计数、不写日志，只返回当前值 */
$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$bots = array('bot','spider','crawler','slurp','curl','wget','python-requests','Googlebot','Baiduspider','facebookexternalhit','Amazonbot','GPTBot','ChatGPT','ClaudeBot','Claude-User','Claude','Perplexity','Grok','OAI','Bytespider','facebook','meta-external','LinkedIn','Twitter','Discord','Slackbot','Telegram','applebot','bingbot','semrush','Ahrefs','archive','Google-Extended','Yandex','Sogou','Lighthouse','headless','phantomjs','javascript:');
$isBot = false;
foreach ($bots as $b) { if (stripos($ua, $b) !== false) { $isBot = true; break; } }

$dir = dirname(__FILE__);
$f = $dir . '/counter.dat';
$n = 0;
if (file_exists($f)) { $raw = trim(file_get_contents($f)); if (is_numeric($raw)) $n = (int)$raw; }

if (!$isBot) {
    /* 写访问日志（JSONL），IP 做 SHA256 哈希保护隐私，绝不存明文 IP */
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $iph = substr(hash('sha256', $ip . 'duipai-salt-2026'), 0, 14);
    $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $entry = array(
        't' => time(),
        'h' => $iph,
        'u' => substr($ua, 0, 160),
        'r' => $ref,
    );
    $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n";
    @file_put_contents($dir . '/visits.log', $line, FILE_APPEND | LOCK_EX);

    $n++;
    @file_put_contents($f, (string)$n);
}
echo json_encode(array('count'=>$n, 'thanks'=>'感谢期待，对拍 Groove'), JSON_UNESCAPED_UNICODE);
?>
