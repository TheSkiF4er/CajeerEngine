<?php
namespace Workflow;

class Workflow
{
    public const DRAFT = 'draft';
    public const REVIEW = 'review';
    public const PUBLISHED = 'published';

    public static function validStates(): array { return [self::DRAFT, self::REVIEW, self::PUBLISHED]; }

    public static function canTransition(string $from, string $to): bool
    {
        $map = [
            self::DRAFT => [self::REVIEW, self::PUBLISHED],
            self::REVIEW => [self::DRAFT, self::PUBLISHED],
            self::PUBLISHED => [self::DRAFT],
        ];
        return in_array($to, $map[$from] ?? [], true);
    }
}
