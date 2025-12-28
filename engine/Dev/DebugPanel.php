<?php
namespace Dev;

class DebugPanel
{
    public static function shouldShow(): bool
    {
        $cfg = Logger::cfg();
        if (!($cfg['enabled'] ?? false)) return false;
        if (!($cfg['debug_panel'] ?? false)) return false;

        // show only when explicitly requested
        return isset($_GET['__debug']) && (string)$_GET['__debug'] === '1';
    }

    public static function render(array $data): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $safe = fn($s)=>htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
        $json = $safe(json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

        echo "<!doctype html><html><head><meta charset='utf-8'><title>CajeerEngine Debug</title>";
        echo "<style>
            body{font-family:system-ui, -apple-system, Segoe UI, Roboto, Arial; margin:20px;}
            .card{border:1px solid #ddd;border-radius:12px;padding:14px;margin-bottom:12px;}
            h1{margin:0 0 12px 0;font-size:22px;}
            pre{white-space:pre-wrap;word-break:break-word;background:#f7f7f7;border-radius:10px;padding:12px;}
            table{border-collapse:collapse;width:100%;}
            th,td{border-bottom:1px solid #eee;padding:8px;text-align:left;font-size:13px;}
            th{background:#fafafa;}
        </style></head><body>";
        echo "<h1>CajeerEngine Devtools — Debug Panel</h1>";

        echo "<div class='card'><b>Total:</b> ".$safe($data['total_ms'] ?? 0)." ms</div>";

        echo "<div class='card'><h3>Request</h3><pre>".$safe(json_encode($data['request'] ?? [], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT))."</pre></div>";

        echo "<div class='card'><h3>Templates</h3>";
        echo "<table><thead><tr><th>Name</th><th>ms</th><th>Vars</th></tr></thead><tbody>";
        foreach (($data['templates'] ?? []) as $t) {
            echo "<tr><td>".$safe($t['name'] ?? '')."</td><td>".$safe($t['ms'] ?? 0)."</td><td>".$safe(implode(',', $t['vars'] ?? []))."</td></tr>";
        }
        echo "</tbody></table></div>";

        echo "<div class='card'><h3>SQL</h3>";
        echo "<table><thead><tr><th>Query</th><th>ms</th></tr></thead><tbody>";
        foreach (($data['sql'] ?? []) as $q) {
            echo "<tr><td><code>".$safe($q['q'] ?? '')."</code></td><td>".$safe($q['ms'] ?? 0)."</td></tr>";
        }
        echo "</tbody></table></div>";

        echo "<div class='card'><h3>Notes</h3><pre>".$safe(json_encode($data['notes'] ?? [], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT))."</pre></div>";

        echo "<div class='card'><h3>Raw JSON</h3><pre>".$json."</pre></div>";
        echo "</body></html>";
        exit;
    }
}
