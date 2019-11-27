<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\DBAL\Types\Type as DBALType;
use Doctrine\ORM\QueryBuilder;

final class StartUntilFilter extends AbstractContextAwareFilter
{
    public const PARAMETER_BETWEEN_START = 'between_start';
    public const PARAMETER_BETWEEN_UNTIL = 'between_until';

    public const DOCTRINE_DATE_TYPES = [
        DBALType::DATE => true,
        DBALType::DATETIME => true,
        DBALType::DATETIMETZ => true,
        DBALType::TIME => true,
        DBALType::DATE_IMMUTABLE => true,
        DBALType::DATETIME_IMMUTABLE => true,
        DBALType::DATETIMETZ_IMMUTABLE => true,
        DBALType::TIME_IMMUTABLE => true,
    ];

    private $FIELDS_MAP = array();

    /**
     * {@inheritdoc}
     */
    public function getDescription(string $resourceClass): array
    {
        return [];
    }

    /**
     * Determines whether the given property refers to a date field.
     */
    protected function isDateField(string $property, string $resourceClass): bool
    {
        return isset(self::DOCTRINE_DATE_TYPES[(string) $this->getDoctrineFieldType($property, $resourceClass)]);
    }

    /**
     * {@inheritdoc}
     */
    protected function filterProperty(string $property, $values, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        //if ($property != "regions") throw new InvalidArgumentException($this->isPropertyMapped($property, $resourceClass));
        // Expect $values to be an array having the period as keys and the date value as values
        if (
            !$this->isPropertyEnabled($property, $resourceClass) ||
            !$this->isPropertyMapped($property, $resourceClass, true) ||
            !$this->isDateField($property . ".start", $resourceClass) ||
            !$this->isDateField($property . ".until", $resourceClass)
        ) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $field = $property;

        if ($this->isPropertyNested($property . ".until", $resourceClass)) {
            [$alias, $field] = $this->addJoinsForNestedProperty($property . ".until", $alias, $queryBuilder, $queryNameGenerator, $resourceClass);
        }

        if (isset($values[self::PARAMETER_BETWEEN_START])) {
            $this->FIELDS_MAP[$property][self::PARAMETER_BETWEEN_START] = $values[self::PARAMETER_BETWEEN_START];
        }
        if (isset($values[self::PARAMETER_BETWEEN_UNTIL])) {
            $this->FIELDS_MAP[$property][self::PARAMETER_BETWEEN_UNTIL] = $values[self::PARAMETER_BETWEEN_UNTIL];
        }

        $this->addWhere(
            $queryBuilder,
            $queryNameGenerator,
            $alias,
            $field,
            $this->FIELDS_MAP[$property]
        );
    }

    /**
     * Adds the where clause according to the chosen null management.
     *
     * @param string|DBALType $type
     */
    protected function addWhere(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $alias, string $field, array $values, $type = null)
    {
        $type = (string) $type;
        try {
            $start = false === strpos($type, '_immutable') ? new \DateTime($values[self::PARAMETER_BETWEEN_START]) : new \DateTimeImmutable($values[self::PARAMETER_BETWEEN_START]);
            $until = false === strpos($type, '_immutable') ? new \DateTime($values[self::PARAMETER_BETWEEN_UNTIL]) : new \DateTimeImmutable($values[self::PARAMETER_BETWEEN_UNTIL]);
        } catch (\Exception $e) {
            // Silently ignore this filter if it can not be transformed to a \DateTime
            $this->logger->notice('Invalid filter ignored', [
                'exception' => new InvalidArgumentException(sprintf('The field "%s" has a wrong date format. Use one accepted by the \DateTime constructor', $field)),
            ]);

            return;
        }

        $startParameter = $queryNameGenerator->generateParameterName("start");
        $untilParameter = $queryNameGenerator->generateParameterName("until");

        $queryBuilder->andWhere($queryBuilder->expr()->orX(
            sprintf('%s.%s %s :%s', $alias, "start", ">=", $untilParameter),
            sprintf('%s.%s %s :%s', $alias, "until", "<=", $startParameter),
            $queryBuilder->expr()->isNull(sprintf('%s.%s', $alias, "start")),
            $queryBuilder->expr()->isNull(sprintf('%s.%s', $alias, "until"))
        ));

        $queryBuilder->setParameter($startParameter, $start, $type);
        $queryBuilder->setParameter($untilParameter, $until, $type);
    }
}