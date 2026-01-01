<?php
namespace Database;

use Observability\Logger;

class TracingPDO extends \PDO
{
    public int $slowMs = 250;
    public bool $traceQueries = false;

    public function query($statement, ?int $mode = null, ...$fetch_mode_args)
    {
        $t0 = microtime(true);
        $res = parent::query($statement);
        $this->afterQuery((string)$statement, microtime(true)-$t0, null);
        return $res;
    }

    public function prepare($statement, $options = [])
    {
        $stmt = parent::prepare($statement, $options);
        return new TracingPDOStatement($stmt, (string)$statement, $this);
    }

    public function afterQuery(string $sql, float $dt, ?array $params): void
    {
        if (!$this->traceQueries) return;
        $ms = (int)round($dt*1000);
        if ($ms >= $this->slowMs) Logger::warn('db.slow_query', ['ms'=>$ms,'sql'=>$sql,'params'=>$params]);
        else Logger::debug('db.query', ['ms'=>$ms,'sql'=>$sql]);
    }
}

class TracingPDOStatement
{
    private \PDOStatement $stmt;
    private string $sql;
    private TracingPDO $pdo;

    public function __construct(\PDOStatement $stmt, string $sql, TracingPDO $pdo)
    {
        $this->stmt = $stmt; $this->sql = $sql; $this->pdo = $pdo;
    }

    public function execute(?array $params = null): bool
    {
        $t0 = microtime(true);
        $ok = $this->stmt->execute($params);
        $this->pdo->afterQuery($this->sql, microtime(true)-$t0, $params);
        return $ok;
    }

    public function __call($name, $args) { return $this->stmt->{$name}(...$args); }
}
