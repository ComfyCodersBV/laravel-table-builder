<?php

declare(strict_types=1);

use Illuminate\Support\Facades\URL;
use TranquilTools\TableBuilder\AbstractTable;
use TranquilTools\TableBuilder\TableBuilder;
use TranquilTools\TableBuilder\Tests\Fixtures\Category;
use TranquilTools\TableBuilder\Tests\Fixtures\Product;

class CountingChoiceProductsTable extends AbstractTable
{
    public static int $configured = 0;

    public function for()
    {
        return Product::query();
    }

    public function configure(TableBuilder $table): void
    {
        static::$configured++;

        $table->column('name')
            ->selectFilter('sku', [
                'A' => 'A',
                'B' => 'B',
            ])
            ->bulkAction(
                label: 'Move to',
                each: fn (Product $product, string $choice) => $product->update([
                    'category_id' => (int) $choice,
                ]),
                choices: Category::query()
                    ->pluck('name', 'id')
                    ->all(),
            );
    }
}

function countingChoiceUrl(): string
{
    return URL::signedRoute('table.bulk-action', [
        'table' => base64_encode(CountingChoiceProductsTable::class),
        'action' => base64_encode('0'),
        'slug' => 'move-to',
    ]);
}

beforeEach(function () {
    CountingChoiceProductsTable::$configured = 0;

    Category::create(['name' => 'Inbox']);

    Product::create(['name' => 'Chair', 'sku' => 'A']);
    Product::create(['name' => 'Table', 'sku' => 'B']);
});

it('configures the table once for a bulk action on explicit ids', function () {
    $this->postJson(countingChoiceUrl(), [
        'ids' => [1],
        'choice' => '1',
    ])->assertOk();

    expect(CountingChoiceProductsTable::$configured)->toBe(1)
        ->and(Product::query()->find(1)->category_id)->toBe(1);
});
