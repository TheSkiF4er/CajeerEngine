<?php
namespace Seo;

use Core\Config;

class MetaManager
{
    private string $title = '';
    private string $description = '';
    private string $keywords = '';
    private string $canonical = '';
    private array $meta = [];
    private array $og = [];
    private array $twitter = [];
    private array $jsonLd = [];

    public function __construct()
    {
        $this->title = (string)Config::get('seo.default_title', 'CajeerEngine');
        $this->description = (string)Config::get('seo.default_description', '');
    }

    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(string $d): void { $this->description = $d; }
    public function setKeywords(string $k): void { $this->keywords = $k; }
    public function setCanonical(string $url): void { $this->canonical = $url; }

    public function addMeta(string $name, string $content): void { $this->meta[$name] = $content; }
    public function addOg(string $property, string $content): void { $this->og[$property] = $content; }
    public function addTwitter(string $name, string $content): void { $this->twitter[$name] = $content; }

    public function addJsonLd(array $obj): void { $this->jsonLd[] = $obj; }

    public function renderHead(): string
    {
        $site = (string)Config::get('seo.site_name', 'CajeerEngine');
        $title = $this->title ? $this->title . ' — ' . $site : $site;
        $out = [];

        $out[] = '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>';

        if ($this->description !== '') {
            $out[] = '<meta name="description" content="' . htmlspecialchars($this->description, ENT_QUOTES, 'UTF-8') . '">';
        }
        if ($this->keywords !== '') {
            $out[] = '<meta name="keywords" content="' . htmlspecialchars($this->keywords, ENT_QUOTES, 'UTF-8') . '">';
        }
        if ($this->canonical !== '') {
            $out[] = '<link rel="canonical" href="' . htmlspecialchars($this->canonical, ENT_QUOTES, 'UTF-8') . '">';
        }

        foreach ($this->meta as $k => $v) {
            $out[] = '<meta name="' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '" content="' . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . '">';
        }

        // OpenGraph defaults
        $og = array_merge([
            'og:site_name' => $site,
            'og:title' => $this->title ?: $site,
            'og:description' => $this->description ?: '',
            'og:type' => 'website',
        ], $this->og);

        foreach ($og as $k => $v) {
            if ($v === '') continue;
            $out[] = '<meta property="' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '" content="' . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . '">';
        }

        // Twitter defaults
        $tw = array_merge([
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $this->title ?: $site,
            'twitter:description' => $this->description ?: '',
        ], $this->twitter);

        foreach ($tw as $k => $v) {
            if ($v === '') continue;
            $out[] = '<meta name="' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '" content="' . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . '">';
        }

        return implode("\n", $out);
    }

    public function renderJsonLd(): string
    {
        if (!$this->jsonLd) return '';
        $out = [];
        foreach ($this->jsonLd as $obj) {
            $out[] = '<script type="application/ld+json">' . json_encode($obj, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . '</script>';
        }
        return implode("\n", $out);
    }
}
