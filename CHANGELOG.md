# Changelog

All notable changes to `laravel-table-builder` will be documented in this file.

## Unreleased
* The `confirm` of a row action also takes a closure. It gets the row, like `url`, `visible` and `disabled`, and is
  resolved per row, so the payload still carries a string or `null`:
  `confirm: fn (App $app) => "Disconnect {$app->name}?"`. A closure that returns `null` or an empty string asks no
  confirmation for that row. A string confirm works as before.

## 1.0.0 - 2026-10-13
* The PHP side of `tranquil-tools/laravel-vue-table-builder` 1.8.1 plus its unreleased bulk action choices, polling
  and row classes, extracted into a package of its own: `AbstractTable`, `TableBuilder`, `QueryBuilder`, the
  components and concerns, CSV export, the signed bulk-action, reorder and export endpoints, and `make:table`. The
  namespace `TranquilTools\TableBuilder\` is unchanged.
* The payload carries `schemaVersion` (`TableBuilder::SCHEMA_VERSION`, starting at `1.0`) for renderers to check.
* Configuration is read from `config/table-builder.php`. A published `config/vue-table-builder.php` is merged over it
  at registration, logged as deprecated in debug mode, and `config('vue-table-builder.*')` keeps resolving to the merged
  values. Changing `vue-table-builder.*` at runtime no longer affects the tables; set `table-builder.*` instead. List values
  such as `per_page_options` and `routes.middleware` in the legacy file replace the defaults instead of being merged
  by index.
* Translations use the `table-builder::` namespace, with `vue-table-builder::` as an alias. Overrides published in
  `lang/vendor/vue-table-builder` are used until `lang/vendor/table-builder` exists.
* `rowClass()` sends its classes as a list of class names: empty entries and false conditions are dropped, so a
  list like `['', 'font-semibold']` no longer arrives as an object keyed by position.
* The bulk-action and reorder endpoints answer JSON whenever the request expects JSON, without checking for an
  `X-Inertia` header. An Inertia visit asks for HTML, so it still gets the redirect back.
