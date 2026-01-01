<?php
namespace Migration;

class Report
{
    private array $errors = [];
    private array $warnings = [];
    private array $info = [];
    private string $id;

    public function __construct(?string $id = null)
    {
        $this->id = $id ?: date('Ymd_His');
    }

    public function id(): string { return $this->id; }

    public function info(string $msg, array $ctx = []): void { $this->info[] = ['msg'=>$msg,'ctx'=>$ctx,'ts'=>date('c')]; }
    public function warn(string $msg, array $ctx = []): void { $this->warnings[] = ['msg'=>$msg,'ctx'=>$ctx,'ts'=>date('c')]; }
    public function error(string $msg, array $ctx = []): void { $this->errors[] = ['msg'=>$msg,'ctx'=>$ctx,'ts'=>date('c')]; }

    public function ok(): bool { return count($this->errors) === 0; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'generated_at' => date('c'),
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'info' => $this->info,
        ];
    }

    public function save(string $dir): string
    {
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        $file = rtrim($dir,'/') . '/report_' . $this->id . '.json';
        file_put_contents($file, json_encode($this->toArray(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        return $file;
    }
}
