<?php
namespace Permissions;

class Permission
{
    public string $key;
    public function __construct(string $key){ $this->key = $key; }
}
