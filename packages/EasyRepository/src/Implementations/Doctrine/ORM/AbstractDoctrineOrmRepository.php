<?php

declare(strict_types=1);

namespace EonX\EasyRepository\Implementations\Doctrine\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;
use EonX\EasyRepository\Interfaces\DatabaseRepositoryInterface;

abstract class AbstractDoctrineOrmRepository implements DatabaseRepositoryInterface
{
    use DoctrineOrmRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        $entityClass = $this->getEntityClass();

        $this->manager = $registry->getManagerForClass($entityClass);
        $this->repository = $this->manager->getRepository($entityClass);
    }

    abstract protected function getEntityClass(): string;
}
