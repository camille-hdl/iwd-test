<?php
declare(strict_types=1);

if (file_exists(ROOT_PATH.'/vendor/autoload.php') === false) {
    echo "run this command first: composer install";
    exit();
}
require_once ROOT_PATH.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use function Functional\map;
use \IWD\JOBINTERVIEW\Repository\Filesystem\SurveyAnswerRepository;
use \IWD\JOBINTERVIEW\Aggregation\AggregationManager;

$app = new Application();
$repo = new SurveyAnswerRepository(ROOT_PATH . "/data");
$aggregationManager = new AggregationManager();
$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
});

/**
 * Retourner les surveysCodes disponibles
 */
$app->get('/', function () use ($app, $repo) {
    return [
        "codes" => map($repo->getAllSurveyCodes(), function(string $code) {
            return [
                "code" => $code,
                "questionsURI" => implode("/", ["", $code, "questions"])
            ];
        })
    ];
});

/**
 * Retourner les questions disponibles pour le survey demandé.
 * On part du principe qu'on utilise le label d'une question comme identifiant,
 * et que le couple label+type est toujours identique.
 */
$app->get('/{surveyCode}/questions', function ($surveyCode) use ($app, $repo) {
    $results = $repo->filter(function(array $element) use($surveyCode): bool {
        return $element["survey"]["code"] === $surveyCode;
    });
    $questions = [];
    foreach ($results as $result) {
        $label = $result["label"];
        $type = $result["type"];
        if (!isset($questions[$label])) {
            /**
             * Retourner une URI pour récupérer les aggrégations de chaque question
             */
            $questions[$label] = [
                "type" => $type,
                "aggregationsURI" => implode("/", ["", $surveyCode, "question", urlencode($label), "aggregations"])
            ];
        }
    }
    return [
        "surveyCode" => $surveyCode,
        "questions" => $questions
    ];
});

/**
 * Retourner les aggrégations disponibles pour une question
 */
$app->get('/{surveyCode}/question/{questionLabel}/aggregations', function ($surveyCode, $questionLabel) use ($app, $repo, $aggregationManager) {
    $questionLabel = urldecode($questionLabel);
    $results = $repo->filter(function(array $element) use($surveyCode, $questionLabel): bool {
        return $element["survey"]["code"] === $surveyCode && $element["label"] === $questionLabel;
    });
    $aggregations = [];
    if (count($results) > 0) {
        /**
         * Retourner les uri pour chaque aggrégation
         */
        $aggregations = map(
            $aggregationManager->getAvailableAggregations($results),
            function(string $aggregationType) use($surveyCode, $questionLabel): string {
                return implode("/", ["", $surveyCode, "question", urlencode($questionLabel), "aggregation", $aggregationType]);
            }
        );
    }
    return [
        "surveyCode" => $surveyCode,
        "question" => $questionLabel,
        "aggregations" => $aggregations
    ];
});

/**
 * Retourner le résultat de l'aggrégation demandée sur une question
 */
$app->get('/{surveyCode}/question/{questionLabel}/aggregation/{aggregationType}', function ($surveyCode, $questionLabel, $aggregationType) use ($app, $repo, $aggregationManager) {
    $questionLabel = urldecode($questionLabel);
    $results = $repo->filter(function(array $element) use($surveyCode, $questionLabel): bool {
        return $element["survey"]["code"] === $surveyCode && $element["label"] === $questionLabel;
    });
    return [
        "surveyCode" => $surveyCode,
        "question" => $questionLabel,
        "aggregationType" => $aggregationType,
        "aggregation" => $aggregationManager->getResult($results, $aggregationType)
    ];
});

$app->view(function (array $controllerResult) use ($app) {
    return $app->json($controllerResult);
});
$app->error(function (\Exception $e, Request $request, $code) {
    return new Response($e->getMessage());
});

$app->run();

return $app;
