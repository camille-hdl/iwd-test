<?php

declare(strict_types=1);

namespace IWD\JOBINTERVIEW\Aggregation;
use function Functional\every;
use function Functional\reduce_left;
use function Functional\zip;

/**
 * Pour une question QCM,
 * retourner, pour chaque option distincte,
 * le nombre de réponses positives.
 * e.g. : `[ "Reponse1" => 123, "Reponse2" => 3, "Reponse3" => 10]`
 */
class Terms implements \IWD\JOBINTERVIEW\Aggregation\AggregationInterface
{
    const SUPPORTED_TYPE = "qcm";

    /**
     * @see \IWD\JOBINTERVIEW\Aggregation\AggregationInterface
     */
    public static function getName(): string
    {
        return "terms";
    }

    /**
     * On ne peut aggréger que des réponses à des questions QCM
     *
     * @param array $resultSet
     * @return boolean
     */
    public static function supports(array $resultSet): bool
    {
        $isOfSupportedType = function(array $surveyAnswer): bool {
            return $surveyAnswer["type"] === self::SUPPORTED_TYPE;
        };
        return every($resultSet, $isOfSupportedType);
    }

    /**
     * Retourner un tableau associatif avec le nombre de réponses positives pour
     * chaque option du QCM.
     *
     * @param array $resultSet
     * @return array
     */
    public static function result(array $resultSet)
    {
        /**
         * `$_i` et `$_c` représentent l'index et la collection entière et ne sont pas utilisés
         */
        return reduce_left($resultSet, function(array $element, $_i, $_c, $reduction) {
            /**
             * `[["Reponse1", true], ["Reponse2", false], ...]`
             */
            $terms = zip($element["options"], $element["answer"]);
            return reduce_left($terms, function(array $term, $_i, $_c, $reduction) {
                $option = $term[0];
                $answer = $term[1];
                /**
                 * On créé la clé `$reduction[$option]` dans tous les cas
                 * pour avoir toutes les options de façon exhaustive dans le tableau final,
                 * mais on incrémente que si la réponse est positive
                 */
                if (!isset($reduction[$option])) {
                    $reduction[$option] = 0;
                }
                if ($answer) {
                    ++$reduction[$option];
                }
                return $reduction;
            }, $reduction);
        }, []);
    }
}