<?php
/* 对拍 Groove · 站点访客数据看板。只读，不暴露明文 IP。可加 ?key=xxx 简单隔离。 */
$dir = dirname(__FILE__);
$cnt = 0;
if (file_exists($dir.'/counter.dat')) { $raw = trim(@file_get_contents($dir.'/counter.dat')); if (is_numeric($raw)) $cnt = (int)$raw; }
$entries = array();
if (file_exists($dir.'/visits.log')) {
    foreach (@file($dir.'/visits.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        $e = json_decode($l, true);
        if ($e && isset($e['t'])) $entries[] = $e;
    }
}
$todayStart = strtotime('today');
$today = 0; $yesterday = 0;
$perDay = array();
for ($i=6; $i>=0; $i--) $perDay[date('Y-m-d', strtotime("-$i day"))] = 0;
$browsers = array(); $refs = array();
foreach ($entries as $e) {
    $d = date('Y-m-d', $e['t']);
    if (isset($perDay[$d])) $perDay[$d]++;
    if ($e['t'] >= $todayStart) $today++;
    elseif ($e['t'] >= $todayStart - 86400) $yesterday++;
    $ua = $e['u'];
    if      (stripos($ua,'MicroMessenger')!==false) $b='微信';
    elseif  (stripos($ua,'Edg')!==false) $b='Edge';
    elseif  (stripos($ua,'QQBrowser')!==false) $b='QQ浏览器';
    elseif  (stripos($ua,'OPR')!==false) $b='Opera';
    elseif  (stripos($ua,'SamsungBrowser')!==false) $b='三星';
    elseif  (stripos($ua,'UCBrowser')!==false) $b='UC';
    elseif  (stripos($ua,'MiuiBrowser')!==false) $b='小米';
    elseif  (stripos($ua,'Chrome')!==false) $b='Chrome';
    elseif  (stripos($ua,'Safari')!==false) $b='Safari';
    elseif  (stripos($ua,'Firefox')!==false) $b='Firefox';
    elseif  (stripos($ua,'curl')!==false) $b='curl/工具';
    else $b='其他';
    $browsers[$b] = isset($browsers[$b])?$browsers[$b]+1:1;
    if (!empty($e['r']) && stripos($e['r'],'duipai.top')===false) {
        $host = parse_url($e['r'], PHP_URL_HOST);
        if ($host) $refs[$host] = isset($refs[$host])?$refs[$host]+1:1;
    }
}
arsort($browsers); arsort($refs);
$maxDay = max(1, max($perDay));
// 简答题：recent 30
$recent = array_slice(array_reverse($entries), 0, 20);
$maxBar = max(1, max($browsers));
?>
<!doctype html>
<html lang="zh-CN"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>对拍 Groove · 站点数据</title>
<style>
:root{--paper:#F7F8F6;--ink:#11161A;--muted:#6F7777;--line:#DDE3DF;--teal:#0FA89A;--audio:#20CDBD;--soft:#ECF7F4}
*{box-sizing:border-box}
body{margin:0;background:var(--paper);color:var(--ink);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Microsoft YaHei",sans-serif;line-height:1.6}
.wrap{width:min(1080px,calc(100% - 40px));margin:0 auto;padding:44px 0 60px}
.mk{font-size:12px;letter-spacing:.16em;color:var(--teal);font-weight:600;text-transform:uppercase}
h1{margin:8px 0 4px;font-size:32px;letter-spacing:-.03em;font-weight:700}
.sub{color:var(--muted);margin:0 0 26px}
.cards{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:26px}
.card{background:#fff;border:1px solid var(--line);border-radius:20px;padding:20px}
.card .lbl{font-size:13px;color:var(--muted)}
.card .num{font-size:34px;font-weight:700;margin-top:8px;letter-spacing:-.02em}
.card .num.teal{color:var(--teal)}
.panel{background:#fff;border:1px solid var(--line);border-radius:22px;padding:24px;margin-bottom:18px}
.panel h2{font-size:17px;margin:0 0 16px;font-weight:700}
.bar-row{display:flex;align-items:center;gap:12px;margin-bottom:10px}
.bar-row .lab{width:96px;font-size:13px;color:#4B5654;flex:none}
.bar-row .bar{height:18px;background:var(--line);border-radius:999px;flex:1;overflow:hidden}
.bar-row .bar i{display:block;height:100%;background:linear-gradient(90deg,var(--teal),var(--audio));border-radius:999px}
.bar-row .val{width:52px;text-align:right;font-size:13px;font-weight:600;flex:none}
.days{display:grid;grid-template-columns:repeat(7,1fr);gap:12px;text-align:center}
.day .n{font-size:22px;font-weight:700;color:var(--teal)}
.day .d1{height:10px;border-radius:999px;background:var(--soft);margin:8px 0}
.day .d1 i{display:block;height:100%;background:var(--teal);border-radius:999px}
.day .dt{font-size:11px;color:var(--muted)}
table{width:100%;border-collapse:collapse;font-size:13px}
th,td{text-align:left;padding:9px 8px;border-bottom:1px solid var(--line)}
th{color:var(--muted);font-weight:600;font-size:11px;letter-spacing:.04em}
td .gh{font-family:ui-monospace,Consolas,monospace;color:var(--teal)}
.badge{display:inline-block;padding:2px 8px;border-radius:999px;background:var(--soft);color:#2F6B64;font-size:11px;font-weight:600}
.note{color:var(--muted);font-size:12px;margin-top:8px}
footer{color:#8A9394;font-size:12px;padding-top:10px;border-top:1px solid var(--line)}
@media(max-width:720px){.cards{grid-template-columns:1fr 1fr}}
</style></head>
<body><div class="wrap">
<div class="mk">Duipai.top · Official</div>
<h1>站点访客数据</h1>
<p class="sub">实时读取 · 仅统计真实浏览器访问（AI 爬虫已在服务端过滤）· 访客 IP 经哈希脱敏</p>

<div class="cards">
  <div class="card"><div class="lbl">累计访客</div><div class="num teal"><?php echo number_format($cnt); ?></div></div>
  <div class="card"><div class="lbl">今日访客</div><div class="num"><?php echo $today; ?></div></div>
  <div class="card"><div class="lbl">昨日访客</div><div class="num"><?php echo $yesterday; ?></div></div>
  <div class="card"><div class="lbl">近 7 日合计</div><div class="num"><?php echo array_sum($perDay); ?></div></div>
</div>

<div class="panel"><h2>近 7 日趋势</h2>
<div class="days">
<?php foreach ($perDay as $d=>$c): $label=substr($d,5); $pct=round($c/$maxDay*100); ?>
  <div class="day"><div class="n"><?php echo $c; ?></div><div class="d1"><i style="width:<?php echo max(4,$pct); ?>%"></i></div><div class="dt"><?php echo $label; ?></div></div>
<?php endforeach; ?>
</div></div>

<div class="panel"><h2>来源浏览器 / 设备分布</h2>
<?php if ($browsers): foreach ($browsers as $b=>$c): $pct=round($c/$maxBar*100); ?>
<div class="bar-row"><div class="lab"><?php echo htmlspecialchars($b); ?></div><div class="bar"><i style="width:<?php echo max(2,$pct); ?>%"></i></div><div class="val"><?php echo $c; ?></div></div>
<?php endforeach; else: ?><p class="note">暂无数据</p><?php endif; ?></div>

<div class="panel"><h2>最近访问</h2>
<?php if ($recent): ?>
<table><thead><tr><th>时间</th><th>访客标识(脱敏)</th><th>浏览器</th><th>来源</th></tr></thead><tbody>
<?php foreach ($recent as $e): ?>
<tr><td><?php echo date('m-d H:i', $e['t']); ?></td><td><span class="gh"><?php echo htmlspecialchars($e['h']); ?></span></td><td><span class="badge"><?php echo htmlspecialchars(substr($e['u'],0,26)); ?></span></td><td><?php echo htmlspecialchars($e['r'] ? parse_url($e['r'], PHP_URL_HOST) : '直接访问'); ?></td></tr>
<?php endforeach; ?>
</tbody></table>
<?php else: ?><p class="note">还没有真实访问记录——把官网分享出去，这里就会实时亮起来。</p><?php endif; ?></div>

<div class="panel"><h2>外部来源站点</h2>
<?php if ($refs): foreach (array_slice($refs,0,10,true) as $h=>$c): ?>
<div class="bar-row"><div class="lab" style="width:190px"><?php echo htmlspecialchars($h); ?></div><div class="bar"><i style="width:<?php echo round($c/max(1,max($refs))*100); ?>%"></i></div><div class="val"><?php echo $c; ?></div></div>
<?php endforeach; else: ?><p class="note">暂无外部来源</p><?php endif; ?></div>

<footer>对拍 Groove · duipai.top · 本站数据仅供站长自用</footer>
</div></body></html>
