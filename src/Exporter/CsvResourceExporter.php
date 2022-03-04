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

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
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
    public function getFormat(): string
    {
        return 'csv';
    }

    public function getContentType(): string
    {
        return 'text/csv';
    }

    protected function exportContent(GridViewInterface $gridView, array $fields): void
    {
        $this->exportHeaders($fields);

        parent::exportContent($gridView, $fields);
    }

    protected function exportResources(GridViewInterface $gridView, iterable $resources, array $fields): void
    {
        foreach ($resources as $resource) {
            $row = [];
            foreach ($fields as $field) {
                $row[] = $this->getFieldValue($gridView, $field, $resource);
            }
            $this->writer->write($row);
        }
    }

    private function exportHeaders(array $fields): void
    {
        $headers = [];

        foreach($fields as $field) {
            $headers[] = $this->getLabel($field);
        }

        $this->writer->write($headers);
    }
}
