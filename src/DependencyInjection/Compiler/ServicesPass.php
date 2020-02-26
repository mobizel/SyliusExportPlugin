<?php

/*
 * This file is part of mz_155_s_rebelote.
 *
 * (c) Mobizel.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Mobizel\SyliusExportPlugin\DependencyInjection\Compiler;

use App\Controller\ProductSlugController;
use App\Doctrine\ORM\Driver;
use App\Export\CsvOrderExporter;
use App\Fixture\ProductAttributeFixture;
use App\Fixture\ProductFixture;
use App\EventListener\UserImpersonatedListener;
use App\Fixture\TaxonFixture;
use App\Form\Type\Attribute\AttributeValueType;
use App\Form\Type\Attribute\IntegerAttributeType;
use App\Form\Type\Attribute\PercentAttributeType;
use App\Form\Type\Attribute\SelectAttributeType;
use App\Form\Type\Attribute\TextareaAttributeType;
use App\Form\Type\Attribute\TextAttributeType;
use App\Payum\MangoPay\Action\ResolveNextRouteAction;
use App\Sitemap\Provider\ProductUrlProvider;
use App\Sitemap\Provider\TaxonUrlProvider;
use App\Tests\Behat\Context\Ui\Admin\ManagingProductAttributesContext;
use App\Tests\Behat\Context\Ui\EmailContext;
use App\Tests\Behat\Context\Ui\Shop\AccountContext;
use App\Tests\Behat\Context\Ui\Shop\LoginContext;
use App\Tests\Behat\Context\Ui\Shop\RegistrationContext;
use App\Tests\Behat\Element\Shop\ChangePasswordFormElement;
use App\Tests\Behat\Element\Shop\RegisterElement;
use App\Validator\Constraints\LocalesAwareValidAttributeValueValidator;
use Mobizel\SyliusExportPlugin\Controller\ResourceController;
use Mobizel\SyliusExportPlugin\Exporter\CsvExporter;
use Mobizel\SyliusExportPlugin\Exporter\ExporterInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processController($container);
    }

    protected function processController(ContainerBuilder $container): void
    {
        $csvExporter = $container->findDefinition(ExporterInterface::class);
        $customerController = $container->getDefinition('sylius.controller.customer');

        $customerController->setClass(ResourceController::class);
        $customerController->addMethodCall('setExporter', [$csvExporter]);
    }
}
