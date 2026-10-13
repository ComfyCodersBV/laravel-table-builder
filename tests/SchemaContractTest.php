<?php

declare(strict_types=1);

use TranquilTools\TableBuilder\TableBuilder;

it('emits the schema version every renderer checks against', function () {
    $payload = TableBuilder::for([])->toArray();

    expect($payload['schemaVersion'])->toBe(TableBuilder::SCHEMA_VERSION)
        ->and($payload['schemaVersion'])->toMatch('/^\d+\.\d+$/');
});
