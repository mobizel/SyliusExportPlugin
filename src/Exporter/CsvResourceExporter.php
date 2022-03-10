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

use Sylius\Component\Grid\View\GridViewInterface;

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
