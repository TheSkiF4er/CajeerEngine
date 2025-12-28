<?php
namespace Core;

class Container
{
    private array $bindings = [];
    private array $singletons = [];
    private array $instances = [];

    public function bind(string $id, callable $factory): void { $this->bindings[$id] = $factory; }
    public function singleton(string $id, callable $factory): void { $this->singletons[$id] = $factory; }
    public function instance(string $id, mixed $obj): void { $this->instances[$id] = $obj; }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]) || isset($this->singletons[$id]) || isset($this->bindings[$id]);
    }

    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) return $this->instances[$id];
        if (isset($this->singletons[$id])) {
            $this->instances[$id] = ($this->singletons[$id])($this);
            return $this->instances[$id];
        }
        if (isset($this->bindings[$id])) return ($this->bindings[$id])($this);
        throw new \RuntimeException('Container: service not found: ' . $id);
    }
}
