<?php 

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \IWD\JOBINTERVIEW\Repository\Filesystem\SurveyAnswerRepository;
use function Functional\head;

final class SurveyAnswerRepositoryTest extends TestCase
{
    public function testLoad(): void
    {
        $path = __DIR__ . "/./data/";
        $repository = new SurveyAnswerRepository($path);
        $this->assertCount(2, $repository->all(), "Le fichier corrompu ne devrait pas être chargé");
    }

    public function testFilter(): void
    {
        $path = __DIR__ . "/./data/";
        $repository = new SurveyAnswerRepository($path);
        $answers = $repository->filter(function(array $element): bool {
            return true;
        });
        $this->assertGreaterThan(0, count($answers), "On devrait avoir au moins 1 résultat");
        $answer = head($answers);
        $this->assertArrayHasKey("survey", $answer);
        $this->assertArrayHasKey("type", $answer);
        $this->assertArrayHasKey("options", $answer);
        $this->assertArrayHasKey("label", $answer);
        $answers = $repository->filter(function(array $element): bool {
            return false;
        });
        $this->assertCount(0, $answers, "On ne devrait pas avoir de résultat");
    }

}