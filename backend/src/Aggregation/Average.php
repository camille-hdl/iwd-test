<?php

declare(strict_types=1);

namespace IWD\JOBINTERVIEW\Aggregation;

use function Functional\every;
use function Functional\map;

/**
 * Pour une question numeric,
 * Retourner la moyenne des réponses
 */
class Average implements \IWD\JOBINTERVIEW\Aggregation\AggregationInterface
{
    const SUPPORTED_TYPE = "numeric";

    /**
     * @see \IWD\JOBINTERVIEW\Aggregation\AggregationInterface
     */
    public static function getName(): string
    {
        return "average";
    }

    /**
     * On ne peut aggréger que des réponses à des questions numeric
     *
     * @param array $resultSet
     * @return boolean
     */
    public static function supports(array $resultSet): bool
    {
        $isOfSupportedType = function (array $surveyAnswer): bool {
            return $surveyAnswer["type"] === self::SUPPORTED_TYPE;
        };
        return every($resultSet, $isOfSupportedType);
    }

    /**
     * Retourner un seul float qui représente la moyenne des réponses.
     * Retourne null s'il n'y a pas de réponse.
     *
     * @param array $resultSet
     * @return float|null
     */
    public static function result(array $resultSet)
    {
        $answers = map($resultSet, function (array $result) {
            return $result["answer"];
        });
        if (count($answers) <= 0) {
            return null;
        }
        return (float)(array_sum($answers) / count($answers));
    }
}
