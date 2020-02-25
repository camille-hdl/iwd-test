<?php

declare(strict_types=1);

namespace IWD\JOBINTERVIEW\Repository\Filesystem;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function Functional\filter;
use function Functional\map;
use function Functional\flat_map;

/**
 * Utiliser un répertoire local contenant des fichiers json
 * comme stockage des données de SurveyAnswers.
 *
 * Je préfère travailler avec la réponses à une question comme entité plutôt que
 * le fichier json entier, qui correponse à la réponse à un sondage.
 * Il faut donc "applatir" les fichiers dans `SurveyAnswerRepository::load()`
 */
class SurveyAnswerRepository implements \IWD\JOBINTERVIEW\Repository\RepositoryInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array[]
     */
    protected $surveyAnswers = [];

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->load();
    }

    /**
     * Lire le dossier fourni et charger les surveyAnswers en mémoire
     *
     * @return void
     */
    protected function load()
    {
        $finder = new Finder();
        /**
         * Retourne true si $file est un fichier json décrivant
         * un SurveyAnswer exploitable
         */
        $isValidSurveyAnswer = function (SplFileInfo $file): bool {
            $jsonContent = json_decode($file->getContents(), true);
            /**
             * Cas d'erreur:
             * TODO: tester le format de façon plus stricte
             */
            if (false === $jsonContent) {
                return false;
            }
            if (!isset($jsonContent["survey"])) {
                return false;
            }
            if (!isset($jsonContent["questions"])) {
                return false;
            }

            return true;
        };
        $finder->files()->name('*.json')->filter($isValidSurveyAnswer)->sortByName(true)->in($this->path);
        /**
         * Lister les réponses à chaque question "à plat".
         * TODO: contrôler la confirmité des `questions`
         */
        $this->surveyAnswers = flat_map($finder, function ($file) {
            $surveyData = json_decode($file->getContents(), true);
            return map(
                $surveyData["questions"],
                function ($question) use ($surveyData, $file) {
                    $question["survey"] = $surveyData["survey"];
                    $question["answerId"] = $file->getFilenameWithoutExtension();
                    return $question;
                }
            );
        });
    }

    /**
     * @see \IWD\JOBINTERVIEWRepository\RepositoryInterface
     */
    public function filter(callable $predicate): array
    {
        /**
         * j'appelle `array_values()` pour s'assurer d'avoir toujours un array
         * avec des indices séquentiels à partir de 0
         */
        return array_values(filter($this->surveyAnswers, $predicate));
    }

    /**
     * @see \IWD\JOBINTERVIEWRepository\RepositoryInterface
     */
    public function all(): array
    {
        return $this->surveyAnswers;
    }

    /**
     * Retourne les surveyCodes distincts
     *
     * @return array
     */
    public function getAllSurveyCodes(): array
    {
        return array_values(array_unique(map($this->all(), function (array $result) {
            return $result["survey"]["code"];
        })));
    }
}
