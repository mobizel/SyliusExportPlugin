<?php

/*
 * This file is part of sylius_export_plugin.
 *
 * (c) Mobizel
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Mobizel\SyliusExportPlugin\Exporter;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

class CsvResourceExporter extends AbstractResourceExporter
{
    /** @var  resource */
    private $handle;

    public function export(GridViewInterface $gridView): string
    {
        $definition = $gridView->getDefinition();

        ob_start();
        $handle = fopen('php://output', 'w');

        if (false === $handle) {
            throw new \Exception('Error opening console output');
        }

        $this->handle = $handle;

        $this->exportHeaders($definition);
        $this->exportContent($gridView);

        fclose($this->handle);
        $content = ob_get_clean();

        return false === $content ? '' : $content;
    }

    protected function exportHeaders(Grid $definition): void
    {
        $headers = [];
        $fields = $definition->getEnabledFields();

        $this->sortFields($fields);

        foreach($fields as $field) {
            $headers[] = $this->getLabel($field);
        }

        fputcsv($this->handle, $headers);
    }

    protected function exportResources(GridViewInterface $gridView, $resources, array $fields): void
    {
        foreach ($resources as $resource) {
            $row = [];
            foreach ($fields as $field) {
                $row[] = $this->getFieldValue($gridView, $field, $resource);
            }
            fputcsv($this->handle, $row);
        }
    }

    public function getFormat(): string
    {
        return 'csv';
    }
}
