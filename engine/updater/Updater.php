<?php
namespace Updater;

class Updater
{
    public function checkRemote(): array
    {
        // TODO: реализовать получение манифестов (stable/beta) через HTTPS
        return [];
    }

    public function applyPackage(string $file): bool
    {
        // TODO: реализовать распаковку .cajeerpatch, backup, apply patch, scripts
        return true;
    }
}
