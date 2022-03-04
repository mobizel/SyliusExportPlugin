<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Mobizel Export plugin</h1>

<p align="center">
    <a href="https://packagist.org/packages/mobizel/sylius-export-plugin" title="Version">
        <img src="https://img.shields.io/packagist/v/mobizel/sylius-export-plugin.svg" />
    </a>
    <a href="http://travis-ci.org/mobizel/sylius-export-plugin" title="Build status">
        <img src="https://travis-ci.org/mobizel/SyliusExportPlugin.svg?branch=master" />
    </a>
</p>

## Getting started

<p>This plugin add a new bulkAction 'export' to all Sylius resources.<br/>
It use the default resource grid definition to export data.<br/>
You can also use a specific grid for export.<br/><br/>
</p>

It allow you to export:
* All entities
* All entities filtered by search
* Selected entities (with checkbox)

IMPORTANT: This plugin does not depend on ````sylius/sylius```` but only ```sylius/resource-bundle``` and ```sylius/grid-bundle```, so it can be used in other project like the symfony starter [monofony](https://github.com/Monofony/Monofony). 

## Installation

1. Require and install the plugin

  - Run `composer require mobizel/sylius-export-plugin`

2. Register the bundle:

=> Integration with ````sylius/sylius```` (full e-commerce framework)

```php
<?php

// config/bundles.php

return [
    // ...
   Mobizel\SyliusExportPlugin\MobizelSyliusExportPlugin::class => ['all' => true],
];
```

=> Integration with third party that use ````sylius/resource-bundle```` (like monofony)

```php
<?php

// config/bundles.php

return [
    // ...
   Mobizel\SyliusExportPlugin\MobizelSyliusExportBundle::class => ['all' => true],
];
```

## Configuration

### GRID configuration:

Create file ``` config/packages/sylius_grid.yaml``` if not exist and add new bulk action

=> With ````sylius/sylius```` (full e-commerce framework)

````yaml
sylius_grid:
    templates:
        bulk_action:
            export: "@MobizelSyliusExportPlugin/Admin/Grid/BulkAction/export.html.twig"
````

=> With third party that use ````sylius/resource-bundle```` (like monofony)

````yaml
sylius_grid:
    templates:
        bulk_action:
            export: "@MobizelSyliusExport/Admin/Grid/BulkAction/export.html.twig"
````

### Add new button macro

Add this following file to add the new button macro.

````twig
# templates/bundles/SyliusUiBundle/Macro/buttons.html.twig

{% extends "@!SyliusUi/Macro/buttons.html.twig" %}

{% macro bulkExport(url, message, labeled = true) %}
    <form action="{{ url }}" method="post" id="bulk-export">
        <button class="ui red {% if labeled %}labeled {% endif %}icon button not_disabled" type="submit">
            <i class="icon download"></i> {{ ((message is empty and labeled) ? 'sylius.ui.export' : message)|trans }}
        </button>
    </form>
{% endmacro %}

````

### Javascript integration

Integrate ```vendor/mobizel/sylius-export-plugin/src/Resources/private/js/bulk-export.js``` in your javascript build (webpack / gulp) or directly in twig (you need to copy file to your assets directory)

## How to use it

### Example

You only have to add export bulk action to your grid, example with customer grid, create file ```config/grids/admin/customer.yaml``` to override customer's grid:

````yaml
sylius_grid:
    grids:
        sylius_admin_customer:
            actions:
                bulk:
                    export:
                        type: export
````

Next, enable your grid.<br/>
Edit ```config/packages/sylius_grid.yaml``` and add on the top:

````yaml
imports:
  - { resource: '../grids/admin/customer.yaml' }
````

### How to enable export of selected entities

Export of selected entities does not work out of the box. You need to override the entity repository.<br>
Example for customer:

* Create CustomerRepository class:
````php
<?php
declare(strict_types=1);

namespace Tests\Mobizel\SyliusExportPlugin\Application\src\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as BaseCustomerRepository;

class CustomerRepository extends BaseCustomerRepository
{
    public function createListQueryBuilderFilteredByIds(?array $ids): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o');

        if (null !== $ids && count($ids) > 0) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('o.id', ':ids'))
                ->setParameter('ids', $ids);
        }

        return $queryBuilder;
    }
}
````
Note: We add new method to fetch entities filtered by id

* Create file ````config/packages/sylius_customer```` if not exist

* Set custom repository in this file

```yaml
sylius_customer:
    resources:
        customer:
            classes:
                repository: Repository\CustomerRepository
```

* Update customer grid to user new method:

````yaml
sylius_grid:
    grids:
        sylius_admin_customer:
            driver:
                options:
                    class: "%sylius.model.customer.class%" OR App\Entity\Customer\Customer
                    repository:
                        method: createListQueryBuilderFilteredByIds
                        arguments:
                            - $ids
````

complete file:
````yaml
sylius_grid:
    grids:
        sylius_admin_customer:
            driver:
                options:
                    class: "%sylius.model.customer.class%" OR App\Entity\Customer\Customer
                    repository:
                        method: createListQueryBuilderFilteredByIds
                        arguments:
                            - $ids
            actions:
                bulk:
                    export:
                        type: export
````

### How to use custom grid

If you want to use a custom grid while export entites, you just have to override the route and specify the grid paramter, example for customer:

````yaml
sylius_backend_customer_bulk_export:
    path: /customers/bulk-export
    methods: [POST]
    defaults:
        _controller: sylius.controller.customer:exportAction
        _sylius:
            grid: my_custom_grid
        ...
````

## Custom export format

This plugin only use CSV for export, however you can implement your own export.
Create a new class that implements ```Mobizel\SyliusExportPlugin\Exporter\ResourceExporterInterface```.

If you create an ```XmlResourceExport``` with method
````php
    public function getFormat(): string
    {
        return 'xml';
    }
````

you can change the export format in the route definition:
````yaml
sylius_backend_customer_bulk_export:
    path: /customers/bulk-export
    methods: [POST]
    defaults:
        _controller: sylius.controller.customer:exportAction
        _sylius:
            grid: my_custom_grid
            vars:
                export_format: xml
        ...
````
Contributing
------------

Would like to help us ? Feel free to open a pull-request!

License
-------

Sylius export plugin is completely free and released under the MIT License.

Authors
-------

Sylius export plugin was originally created by [Kévin REGNIER](https://twitter.com/SmilDev).
