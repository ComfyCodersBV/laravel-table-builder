<?php

declare(strict_types=1);

namespace TranquilTools\TableBuilder\Support;

final class TableTranslations
{
    public const NAMESPACE = 'table-builder';

    public const LEGACY_NAMESPACE = 'vue-table-builder';

    private static string $namespace = self::NAMESPACE;

    public static function useNamespace(string $namespace): void
    {
        self::$namespace = $namespace;
    }

    public static function namespace(): string
    {
        return self::$namespace;
    }

    /**
     * @param  array<string, string|int|float>  $replace
     * @return string|array<string, mixed>
     */
    public static function get(string $key, array $replace = []): string|array
    {
        return trans(sprintf('%s::table.%s', self::$namespace, $key), $replace);
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return (array) trans(sprintf('%s::table', self::$namespace));
    }
}
