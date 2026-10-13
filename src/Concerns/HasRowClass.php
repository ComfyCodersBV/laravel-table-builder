<?php

declare(strict_types=1);

namespace TranquilTools\TableBuilder\Concerns;

use Closure;
use LogicException;

trait HasRowClass
{
    protected ?Closure $rowClassCallback = null;

    public function rowClass(callable $callback): self
    {
        $this->rowClassCallback = Closure::fromCallable($callback);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $itemArray
     * @return array<string, mixed>
     */
    protected function applyRowClass(array $itemArray, mixed $item): array
    {
        if (is_null($this->rowClassCallback)) {
            return $itemArray;
        }

        if (array_key_exists('__class', $itemArray)) {
            throw new LogicException('A row already has a "__class" value, which rowClass() would overwrite. Rename that column or alias it in the query.');
        }

        $class = call_user_func($this->rowClassCallback, $item);

        if (is_array($class)) {
            $class = $this->classNames($class);
        }

        if (empty($class)) {
            return $itemArray;
        }

        $itemArray['__class'] = $class;

        return $itemArray;
    }

    /**
     * @param  array<array-key, mixed>  $classes
     * @return list<string>
     */
    private function classNames(array $classes): array
    {
        return collect($classes)
            ->flatMap(fn (mixed $value, int|string $key): array => match (true) {
                is_array($value) => $this->classNames($value),
                is_int($key) => [$value],
                default => [$value ? $key : null],
            })
            ->filter(fn (mixed $class): bool => is_string($class) && $class !== '')
            ->values()
            ->all();
    }
}
