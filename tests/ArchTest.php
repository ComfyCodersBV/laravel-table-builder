<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('the core does not depend on a frontend')
    ->expect('TranquilTools\TableBuilder')
    ->not->toUse([
        'Inertia',
    ]);

it('mentions no frontend in its sources', function () {
    $sources = collect(File::allFiles(dirname(__DIR__).'/src'))
        ->merge(File::allFiles(dirname(__DIR__).'/routes'))
        ->merge(File::allFiles(dirname(__DIR__).'/config'))
        ->filter(fn (SplFileInfo $file): bool => preg_match('/inertia|resources\/js|\.vue\b|\bvue\b(?!-table-builder)/i', File::get($file->getPathname())) === 1)
        ->map(fn (SplFileInfo $file): string => $file->getFilename())
        ->values()
        ->all();

    expect($sources)->toBe([]);
});
