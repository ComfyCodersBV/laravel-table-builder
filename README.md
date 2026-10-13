# A TableBuilder core package for Laravel

Define Laravel tables in PHP: columns, filters, search, sorting, pagination, totals, bulk and row actions, CSV export
and reordering. This package knows nothing about any frontend — it produces a JSON payload that a renderer consumes,
and serves the signed bulk-action, reorder and export endpoints.

## You probably want the Vue package instead

Unless you are writing a renderer, install
**[tranquil-tools/laravel-vue-table-builder](https://gitlab.comfycoders.nl/tranquil-tools/laravel-vue-table-builder)**.
It requires this package, so you get both:

```bash
composer require tranquil-tools/laravel-vue-table-builder
```

**Full documentation lives at
[docs.comfycoders.nl](https://docs.comfycoders.nl/laravel-vue-table-builder)**. This package ships no `docs/` directory
on purpose: one documentation site, one version stream, no duplication.

## What lives here

|                            |                                                                                          |
|----------------------------|------------------------------------------------------------------------------------------|
| `src/`                     | `AbstractTable`, `TableBuilder`, `QueryBuilder`, components, export, the `make:table` command |
| `src/Http/`, `routes/`     | the signed `table.bulk-action`, `table.reorder` and `table.export` endpoints              |
| `config/table-builder.php` | per page defaults, route middleware, signature lifetime, display timezone, export options |
| `resources/lang/`          | texts the payload carries, such as the search label and date presets                      |

The boundary rule: **what produces the payload belongs here, what renders it belongs in a renderer.**

## The schema contract

`TableBuilder::SCHEMA_VERSION` is emitted as `schemaVersion` in every payload. A renderer compares it against the
version it supports. Bump the major for changes that would misrender in an older renderer, the minor for additions
that can be ignored safely.

## Coming from laravel-vue-table-builder

The namespace `TranquilTools\TableBuilder\` did **not** change, so table classes keep working. Exactly one installed
package may claim that namespace: this one.

- Configuration lives in `config/table-builder.php`. A published `config/vue-table-builder.php` is still read and wins
  over the defaults, and `config('vue-table-builder.*')` still resolves. Rename the file when convenient.
- Translations live under `table-builder::`; `vue-table-builder::` keeps resolving to the same files. Overrides
  published in `lang/vendor/vue-table-builder` keep applying until a `lang/vendor/table-builder` directory exists.

## Credits

Built by [ComfyCoders BV](https://comfycoders.nl). Licensed under MIT — see [LICENSE.md](LICENSE.md).
