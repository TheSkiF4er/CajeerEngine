<?php
namespace Core;
class DebugPanel {
  public static function render(): string {
    $d=Collector::all();
    return '<div style="position:fixed;bottom:12px;right:12px;z-index:9999;background:#111827;color:#e5e7eb;border:1px solid #374151;border-radius:12px;padding:10px 12px;font:12px/1.4 system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;max-width:420px"><b>CajeerEngine Debug</b><div style="opacity:.8">SQL: '.count($d['sql']).' • Events: '.count($d['events']).'</div></div>';
  }
}
