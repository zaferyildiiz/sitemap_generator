<?php
if (isset($_GET['crawl'])) {
    header('Content-Type: text/plain; charset=UTF-8');
    set_time_limit(0);

    // Kullanıcıdan site URL'si al
    $startUrl = isset($_GET['url']) ? rtrim($_GET['url'], '/') : "https://kodlamaklazim.com";
    if (!preg_match('/^https?:\/\//', $startUrl)) {
        $startUrl = "https://" . $startUrl;
    }

    $visited = [];
    $toVisit = [$startUrl];

    function getLinks($url, $base) {
        $html = @file_get_contents($url);
        if ($html === false) return [];

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $links = [];

        foreach ($dom->getElementsByTagName('a') as $a) {
            $href = $a->getAttribute('href');
            if (!$href) continue;

            if (strpos($href, 'http') !== 0) {
                $href = rtrim($base, '/') . '/' . ltrim($href, '/');
            }

            // ?page= gibi parametreleri filtrele
            if (strpos($href, $base) === 0 && strpos($href, '?page=') === false) {
                $links[] = strtok($href, '#');
            }
        }

        return array_unique($links);
    }

    while (!empty($toVisit)) {
        $current = array_shift($toVisit);
        if (in_array($current, $visited)) continue;

        echo $current . "\n";
        flush(); @ob_flush();

        $visited[] = $current;
        $links = getLinks($current, $startUrl);

        foreach ($links as $link) {
            if (!in_array($link, $visited) && !in_array($link, $toVisit)) {
                $toVisit[] = $link;
            }
        }
    }

    // 📁 Sitemap XML oluştur
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($visited as $url) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url, ENT_QUOTES | ENT_XML1, 'UTF-8') . "</loc>\n";
        $xml .= "    <changefreq>monthly</changefreq>\n";
        $xml .= "    <priority>0.64</priority>\n";
        $xml .= "  </url>\n";
    }

    $xml .= '</urlset>';

    file_put_contents(__DIR__ . '/sitemap.xml', $xml);

    // 🧮 Toplam sonuç
    echo "\n\n✅ Toplam bulundu: " . count($visited) . " URL\n";
    echo "🗂️ sitemap.xml dosyasına yazıldı.\n";
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Sitemap Tarayıcı</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 30px; }
    #results { margin-top: 20px; padding: 15px; background: #f9f9f9;
      border: 1px solid #ccc; white-space: pre-line; height: 400px; overflow-y: auto; }
    input { padding: 8px; width: 300px; margin-right: 10px; }
    button { padding: 10px 25px; font-size: 16px; cursor: pointer; }
  </style>
</head>
<body>

<h2>Siteyi Tara ve URL'leri Canlı Gör + sitemap.xml Oluştur</h2>
<input type="text" id="siteUrl" placeholder="https://ornek.com" value="https://kodlamaklazim.com">
<button id="startBtn">Taramayı Başlat</button>
<div id="results">Hazır.</div>

<script>
document.getElementById("startBtn").addEventListener("click", function () {
  const results = document.getElementById("results");
  const url = document.getElementById("siteUrl").value.trim();
  if (!url) {
    alert("Lütfen bir site adresi girin.");
    return;
  }

  results.innerText = "⏳ Taranıyor...\n";

  const xhr = new XMLHttpRequest();
  xhr.open("GET", "?crawl=1&url=" + encodeURIComponent(url), true);
  xhr.onprogress = function () {
    results.innerText = xhr.responseText;
    results.scrollTop = results.scrollHeight;
  };
  xhr.onload = function () {
    results.innerText = xhr.responseText + "\n✅ Tarama tamamlandı.";
  };
  xhr.send();
});
</script>

</body>
</html>
