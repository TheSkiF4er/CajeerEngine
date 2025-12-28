<?php
namespace UIBuilder;
class Schema
{
    public const VERSION = 1;
    public static function defaultLayout(string $title = ''): array
    {
        return [
            'version' => self::VERSION,
            'title' => $title,
            'sections' => [[
                'id' => 'sec_1',
                'class' => 'container py-6',
                'grid' => ['cols' => 12, 'gap' => 4],
                'blocks' => [[
                    'type' => 'text',
                    'id' => 'b_text_1',
                    'col' => ['span' => 12],
                    'props' => ['html' => '<h1>' . htmlspecialchars($title ?: 'New page', ENT_QUOTES) . '</h1>']
                ]],
            ]],
        ];
    }
}
