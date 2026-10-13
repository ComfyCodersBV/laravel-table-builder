<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use TranquilTools\TableBuilder\TableBuilder;
use TranquilTools\TableBuilder\TableBuilderServiceProvider;

it('reads a published vue-table-builder config file', function () {
    config()->set('vue-table-builder', [
        'routes' => [
            'signature_ttl' => 5,
        ],
    ]);

    (new TableBuilderServiceProvider(app()))->register();

    expect(config('table-builder.routes.signature_ttl'))->toBe(5);
});

it('keeps its own defaults for keys the legacy file does not set', function () {
    config()->set('vue-table-builder', [
        'routes' => [
            'signature_ttl' => 5,
        ],
    ]);

    (new TableBuilderServiceProvider(app()))->register();

    expect(config('table-builder.export.chunk_size'))->toBe(1000)
        ->and(config('table-builder.routes.middleware'))->toBe([]);
});

it('still resolves the legacy config key for applications that read it', function () {
    expect(config('vue-table-builder.export.chunk_size'))->toBe(config('table-builder.export.chunk_size'))
        ->and(config('vue-table-builder.per_page_options'))->toBe([10, 25, 50, 100]);
});

it('applies the per page defaults of a published legacy file', function () {
    config()->set('vue-table-builder', [
        'default_per_page' => 5,
        'per_page_options' => [5, 10],
    ]);

    $provider = new TableBuilderServiceProvider(app());
    $provider->register();
    $provider->boot();

    expect(TableBuilder::getDefaultPerPage())->toBe(5);
});

it('still resolves translations under the legacy namespace', function () {
    expect(trans('vue-table-builder::table.search'))
        ->toBe(trans('table-builder::table.search'))
        ->not->toBe('vue-table-builder::table.search');
});

it('keeps applying translations published under the legacy vendor directory', function () {
    $path = lang_path('vendor/vue-table-builder/en/table.php');

    File::ensureDirectoryExists(dirname($path));
    File::put($path, "<?php\n\nreturn ['search' => 'Find'];\n");

    try {
        app('translator')->setLoaded([]);

        $provider = new TableBuilderServiceProvider(app());
        $provider->register();
        $provider->boot();

        expect(TableBuilder::for([])->withGlobalSearch()->toArray()['searchInputs']['global']['label'])->toBe('Find...');
    } finally {
        File::deleteDirectory(lang_path('vendor/vue-table-builder'));
    }
});
