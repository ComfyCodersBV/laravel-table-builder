<?php

declare(strict_types=1);

namespace TranquilTools\TableBuilder;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TranquilTools\TableBuilder\Commands\TableMakeCommand;
use TranquilTools\TableBuilder\Support\TableTranslations;

class TableBuilderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-table-builder')
            ->hasConfigFile('table-builder')
            ->hasTranslations()
            ->hasRoute('web')
            ->hasCommand(TableMakeCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->mergeLegacyConfiguration();
    }

    public function packageBooted(): void
    {
        Lang::addNamespace(TableTranslations::LEGACY_NAMESPACE, __DIR__.'/../resources/lang');

        TableTranslations::useNamespace($this->translationNamespace());

        TableBuilder::defaultPerPageOptions(config('table-builder.per_page_options', [10, 25, 50, 100]));
        TableBuilder::defaultPerPage(config('table-builder.default_per_page', 25));
    }

    private function translationNamespace(): string
    {
        if (File::isDirectory($this->app->langPath('vendor/'.TableTranslations::NAMESPACE))) {
            return TableTranslations::NAMESPACE;
        }

        if (File::isDirectory($this->app->langPath('vendor/'.TableTranslations::LEGACY_NAMESPACE))) {
            return TableTranslations::LEGACY_NAMESPACE;
        }

        return TableTranslations::NAMESPACE;
    }

    private function mergeLegacyConfiguration(): void
    {
        $legacy = config('vue-table-builder');

        if (is_array($legacy) && $legacy !== []) {
            config()->set('table-builder', array_replace_recursive(
                config('table-builder', []),
                $legacy,
            ));

            $this->warnAboutLegacyConfigurationFile();
        }

        config()->set('vue-table-builder', config('table-builder'));
    }

    private function warnAboutLegacyConfigurationFile(): void
    {
        if (! $this->app->hasDebugModeEnabled() || ! File::exists($this->app->configPath('vue-table-builder.php'))) {
            return;
        }

        $this->app->make('log')->warning(
            'config/vue-table-builder.php is deprecated: laravel-table-builder reads config/table-builder.php. Rename the published file; the old name keeps working for now.'
        );
    }
}
