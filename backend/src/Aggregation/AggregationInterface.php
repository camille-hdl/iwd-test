<?php

declare(strict_types=1);

namespace IWD\JOBINTERVIEW\Aggregation;

/**
 * Décrit une classe qui permet de faire une analyse statistiques sur
 * un lot de résultats de SurveyAnswers.
 */
interface AggregationInterface
{
    /**
     * Retourne le type d'aggrégation
     *
     * @return string
     */
    public static function getName(): string;

    /**
     * Retourne `true` si l'aggrégation peut se faire sur le lot de résultat donné
     *
     * @param array[] $resultSet tableau d'elements SurveyAnswers retournés par le repository
     * @return boolean
     */
    public static function supports(array $resultSet): bool;

    /**
     * Retourne le résultat de l'aggrégation
     *
     * @param array[] $resultSet tableau d'elements retournés par le repository
     * @return mixed
     */
    public static function result(array $resultSet);
}
