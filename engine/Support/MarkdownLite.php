<?php
declare(strict_types=1);

namespace Support;

/**
 * Very small Markdown renderer for built-in docs pages.
 * Not a full spec implementation; sufficient for project documentation.
 */
final class MarkdownLite
{
    public static function toHtml(string $md): string
    {
        $md = str_replace("\r\n", "\n", $md);
        $lines = explode("\n", $md);

        $html = '';
        $inCode = false;
        $codeLang = '';
        $listOpen = false;
        $quoteOpen = false;
        $quoteLines = [];

        $flushQuote = function() use (&$html, &$quoteOpen, &$quoteLines): void {
            if (!$quoteOpen) return;
            $quoteOpen = false;
            $text = implode("\n", $quoteLines);
            $quoteLines = [];
            $text = trim($text);
            if ($text === '') return;
            // Render quote body as inline text with <br> line breaks.
            $parts = array_map(function($ln){
                return MarkdownLite::inline($ln);
            }, preg_split('/\R/', $text) ?: []);
            $html .= '<blockquote><p>' . implode("<br>", $parts) . "</p></blockquote>\n";
        };

        foreach ($lines as $line) {
            // fenced code
            if (preg_match('/^```\s*(\w+)?\s*$/', $line, $m)) {
                if (!$inCode) {
                    $inCode = true;
                    $codeLang = $m[1] ?? '';
                    $flushQuote();
                    if ($listOpen) { $html .= "</ul>\n"; $listOpen = false; }
                    $html .= '<pre class="rg-code"><code>';
                } else {
                    $inCode = false;
                    $html .= "</code></pre>\n";
                }
                continue;
            }

            if ($inCode) {
                $html .= htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "\n";
                continue;
            }

            $trim = trim($line);

            // blank line closes list
            if ($trim === '') {
                $flushQuote();
                if ($listOpen) { $html .= "</ul>\n"; $listOpen = false; }
                continue;
            }

            // horizontal rule
            if (preg_match('/^(-{3,}|\*{3,}|_{3,})$/', $trim)) {
                $flushQuote();
                if ($listOpen) { $html .= "</ul>\n"; $listOpen = false; }
                $html .= "<hr>\n";
                continue;
            }

            // blockquote (single-level)
            if (preg_match('/^>\s?(.*)$/', $trim, $m)) {
                if ($listOpen) { $html .= "</ul>\n"; $listOpen = false; }
                $quoteOpen = true;
                $quoteLines[] = (string)$m[1];
                continue;
            }

            // headings
            if (preg_match('/^(#{1,6})\s+(.*)$/', $trim, $m)) {
                $flushQuote();
                if ($listOpen) { $html .= "</ul>\n"; $listOpen = false; }
                $level = strlen($m[1]);
                $text = self::inline($m[2]);
                $id = self::slug(strip_tags($text));
                $html .= sprintf('<h%d id="%s">%s</h%d>' . "\n", $level, $id, $text, $level);
                continue;
            }

            // unordered lists
            if (preg_match('/^[-*]\s+(.*)$/', $trim, $m)) {
                $flushQuote();
                if (!$listOpen) { $html .= "<ul>\n"; $listOpen = true; }
                $html .= '<li>' . self::inline($m[1]) . "</li>\n";
                continue;
            }

            // paragraph
            $flushQuote();
            if ($listOpen) { $html .= "</ul>\n"; $listOpen = false; }
            $html .= '<p>' . self::inline($trim) . "</p>\n";
        }

        $flushQuote();
        if ($listOpen) { $html .= "</ul>\n"; }
        if ($inCode) { $html .= "</code></pre>\n"; }

        return $html;
    }

    private static function inline(string $text): string
    {
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // inline code
        $text = preg_replace('/`([^`]+)`/', '<code class="rg-code-inline">$1</code>', $text) ?? $text;

        // links [text](url)
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text) ?? $text;

        // bold **text**
        $text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text) ?? $text;

        // italic *text*
        $text = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '<em>$1</em>', $text) ?? $text;

        return $text;
    }

    private static function slug(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = preg_replace('/[^\p{L}\p{N}]+/u', '-', $s) ?? $s;
        $s = trim($s, '-');
        return $s !== '' ? $s : 'section';
    }
}
