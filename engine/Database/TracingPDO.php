<?php
namespace Database;

use Observability\Logger;

class TracingPDO extends \PDO
{
    public int $slowMs = 250;
    public bool $traceQueries = false;

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): \PDOStatement|false
    {
        $t0 = microtime(true);
        if ($fetchMode === null) {
            $res = parent::query($query);
        } else {
            $res = parent::query($query, $fetchMode, ...$fetchModeArgs);
        }
        $this->afterQuery($query, microtime(true) - $t0, null);
        return $res;
    }

    public function prepare(string $query, array $options = []): \PDOStatement|false
    {
        $t0 = microtime(true);
        $stmt = parent::prepare($query, $options);
        $this->afterQuery($query, microtime(true) - $t0, null);
        if (!$stmt instanceof \PDOStatement) {
            return false;
        }
        return new TracingPDOStatement($stmt, $query, $this);
    }

    public function afterQuery(string $sql, float $dt, ?array $params): void
    {
        if (!$this->traceQueries) return;
        $ms = (int)round($dt * 1000);
        if ($ms >= $this->slowMs) Logger::warn('db.slow_query', ['ms' => $ms, 'sql' => $sql, 'params' => $params]);
        else Logger::debug('db.query', ['ms' => $ms, 'sql' => $sql]);
    }
}

class TracingPDOStatement extends \PDOStatement
{
    private \PDOStatement $stmt;
    private string $sql;
    private TracingPDO $pdo;

    public function __construct(\PDOStatement $stmt, string $sql, TracingPDO $pdo)
    {
        $this->stmt = $stmt;
        $this->sql = $sql;
        $this->pdo = $pdo;
    }

    public function execute(?array $params = null): bool
    {
        $t0 = microtime(true);
        $ok = $this->stmt->execute($params);
        $this->pdo->afterQuery($this->sql, microtime(true) - $t0, $params);
        return $ok;
    }

    public function __call(string $name, array $args)
    {
        return $this->stmt->{$name}(...$args);
    }
}
