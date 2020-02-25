<?php

declare(strict_types=1);

namespace IWD\JOBINTERVIEW\Aggregation;
use function Functional\filter;
use function Functional\head;
use function Functional\map;

/**
 * Interface "publique" pour utiliser les aggrégations.  
 * permet de rajouter facilement des types d'aggrégation plus tard.
 * Utitisation:
 * ```
 * $manager->getAvailableAggregations($resultSet); // liste des types d'aggrégation possible
 * $result = $manager->getResult($resultSet, "terms");
 * ```
 */
final class AggregationManager
{
    /**
     * @var AggregationInterface[]
     */
    protected $aggregations = [];

    public function __construct()
    {
        $this->aggregations = [
            Terms::class,
            Average::class
        ];
    }

    /**
     * Retourne toutes les aggregations qui peuvent fontionner
     * sur les éléments de $resultSet.
     *
     * @param array $resultSet
     * @return AggregationInterface[]
     */
    protected function _getAvailableAggregations(array $resultSet): array
    {
        return array_values(filter($this->aggregations, function($aggregation) use($resultSet) {
            return $aggregation::supports($resultSet);
        }));
    }

    /**
     * Retourne les types d'aggrégation qui peuvent fonctionner sur resultSet.
     * Les classes `AggregationInterface` ne sont pas exposées publiquement.
     *
     * @param array $resultSet
     * @return string[]
     */
    public function getAvailableAggregations(array $resultSet): array
    {
        return map($this->_getAvailableAggregations($resultSet), function($aggregation) { return $aggregation::getName(); });
    }

    /**
     * Retourne le résultat de l'aggrégation demandée
     *
     * @param array $resultSet
     * @param string $aggregationType
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function getResult(array $resultSet, string $aggregationType)
    {
        $aggregations = filter($this->_getAvailableAggregations($resultSet), function($aggregation) use($aggregationType) {
            return $aggregationType === $aggregation::getName();
        });
        if (count($aggregations) <= 0) {
            throw new \InvalidArgumentException(sprintf("Le type d'aggrégation %s n'existe pas ou n'est pas disponible pour le résultat de recherche", $aggregationType));
        }
        $aggregation = head($aggregations);
        return $aggregation::result($resultSet);
    }
}