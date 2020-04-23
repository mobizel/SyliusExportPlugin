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
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractResourceExporter implements ResourceExporterInterface
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var ServiceRegistryInterface */
    private $fieldsRegistry;

    /** @var GridRendererInterface */
    private $gridRenderer;

    public function __construct(TranslatorInterface $translator, ServiceRegistryInterface $fieldsRegistry, GridRendererInterface $gridRenderer)
    {
        $this->translator = $translator;
        $this->fieldsRegistry = $fieldsRegistry;
        $this->gridRenderer = $gridRenderer;
    }

    abstract public function export(GridViewInterface $gridView): string;

    abstract protected function exportHeaders(Grid $definition): void;

    abstract protected function exportResources(GridViewInterface $gridView, $resources, array $fields): void;

    protected function exportContent(GridViewInterface $gridView): void
    {
        $definition = $gridView->getDefinition();
        $fields = $definition->getEnabledFields();

        $this->sortFields($fields);

        if ($gridView instanceof ResourceGridView) {
            /** @var Pagerfanta $paginator */
            $paginator = $gridView->getData();
            for ($currentPage = 1; $currentPage <= $paginator->getNbPages(); ++$currentPage) {
                $paginator->setCurrentPage($currentPage);
                $this->exportResources($gridView, $paginator->getCurrentPageResults(), $fields);
            }
        } else {
            $this->exportResources($gridView, $gridView->getData(), $fields);
        }
    }

    protected function sortFields(array &$fields): void
    {
        uasort($fields, function (Field $fieldA, Field $fieldB) {
            if ($fieldA->getPosition() == $fieldB->getPosition()) {
                return 0;
            }
            return ($fieldA->getPosition() < $fieldB->getPosition()) ? -1 : 1;
        });
    }
    protected function getLabel(Field $field)
    {
        return $this->translator->trans($field->getLabel());
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldValue(GridViewInterface $gridView, Field $field, $data): string
    {
        $renderedData = $this->gridRenderer->renderField($gridView, $field, $data);
        $renderedData = str_replace(PHP_EOL, "", $renderedData);
        $renderedData = strip_tags($renderedData);

        return $renderedData;
    }
}