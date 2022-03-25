<?php

declare(strict_types=1);

namespace Tests\Mobizel\SyliusExportPlugin\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Mobizel\SyliusExportPlugin\DependencyInjection\MobizelSyliusExportExtension;

final class MobizelSyliusExportExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_registers_settings_delimiter_parameter_with_given_delimiter(): void
    {
        $this->setParameter('sylius.resources', []);

        $this->load([
            'csv_settings' => [
                'delimiter' => ';',
            ],
        ]);

        $this->assertContainerBuilderHasParameter('mobizel.sylius_export_plugin.csv_settings.delimiter', ';');
    }

    /** @test */
    public function it_registers_default_settings_delimiter_parameter(): void
    {
        $this->setParameter('sylius.resources', []);

        $this->load([
            'csv_settings' => [
                'delimiter' => null,
            ],
        ]);

        $this->assertContainerBuilderHasParameter('mobizel.sylius_export_plugin.csv_settings.delimiter', ',');
    }

    /** @test */
    public function it_registers_settings_utf8_encoding_parameter_with_given_utf8_encoding(): void
    {
        $this->setParameter('sylius.resources', []);

        $this->load([
            'csv_settings' => [
                'utf8_encoding' => false,
            ],
        ]);

        $this->assertContainerBuilderHasParameter('mobizel.sylius_export_plugin.csv_settings.utf8_encoding', false);
    }

    /** @test */
    public function it_registers_default_settings_utf8_encoding_parameter(): void
    {
        $this->setParameter('sylius.resources', []);

        $this->load([
            'csv_settings' => [
                'utf8_encoding' => null,
            ],
        ]);

        $this->assertContainerBuilderHasParameter('mobizel.sylius_export_plugin.csv_settings.utf8_encoding', true);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new MobizelSyliusExportExtension(),
        ];
    }
}
