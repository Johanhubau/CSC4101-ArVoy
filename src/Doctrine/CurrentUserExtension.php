<?php
// api/src/Doctrine/CurrentUserExtension.php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Common\PropertyHelperTrait;
use ApiPlatform\Core\Bridge\Doctrine\Orm\PropertyHelperTrait as OrmPropertyHelperTrait;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Comment;
use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\UnavailablePeriod;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    use PropertyHelperTrait;
    use OrmPropertyHelperTrait;
    private $security;

    protected $managerRegistry;

    private const RESSOURCE_CLASSES = array(
        Reservation::class => "client.user",
        Comment::class => "reservation.client.user",
        UnavailablePeriod::class => "room.owner.user"
    );

    public function __construct(Security $security, ManagerRegistry $managerRegistry)
    {
        $this->security = $security;
        $this->managerRegistry = $managerRegistry;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass): void
    {
        if (!array_key_exists($resourceClass, self::RESSOURCE_CLASSES) || $this->security->isGranted('ROLE_MODERATOR')) {
            return;
        }

        if (null === $user = $this->security->getUser()) {
            $queryBuilder->andWhere("0");
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $field = self::RESSOURCE_CLASSES[$resourceClass];
        [$alias, $field] = $this->addJoinsForNestedProperty($field,
        $alias, $queryBuilder, $queryNameGenerator, $resourceClass);

        $queryBuilder->andWhere(sprintf('%s.%s = :current_user', $alias, $field));
        $queryBuilder->setParameter('current_user', $user);
    }

    protected function getManagerRegistry(): ManagerRegistry
    {
        return $this->managerRegistry;
    }
}