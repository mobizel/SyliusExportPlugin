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
