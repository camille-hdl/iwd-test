<?php 

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \IWD\JOBINTERVIEW\Aggregation\AggregationManager;
use \IWD\JOBINTERVIEW\Aggregation\Terms;
use \IWD\JOBINTERVIEW\Aggregation\Average;

final class AggregationManagerTest extends TestCase
{
    public function testGetAvailableAggregations(): void
    {
        $manager = new AggregationManager();
        $input = [
            [
                "type" => "qcm",
                "options" => ["A", "B", "C"],
                "answer" => [true, false, false]
            ],
            [
                "type" => "qcm",
                "options" => ["A", "B", "C", "D"],
                "answer" => [true, true, false, true]
            ]
        ];
        $this->assertNotEmpty($manager->getAvailableAggregations($input));
        $this->assertContains(Terms::getName(), $manager->getAvailableAggregations($input));
        $input = [
            [
                "type" => "number",
                "answer" => 1
            ],
            [
                "type" => "qcm",
                "options" => ["A", "B", "C", "D"],
                "answer" => [true, true, false, true]
            ]
        ];
        $this->assertNotContains(Terms::getName(), $manager->getAvailableAggregations($input));
        $this->assertNotContains(Average::getName(), $manager->getAvailableAggregations($input));
    }
    public function testGetResult(): void
    {
        $manager = new AggregationManager();
        $input = [
            [
                "type" => "qcm",
                "options" => ["A", "B", "C"],
                "answer" => [true, false, false]
            ],
            [
                "type" => "qcm",
                "options" => ["A", "B", "C", "D"],
                "answer" => [true, true, false, true]
            ]
        ];
        $this->assertNotEmpty($manager->getResult($input, Terms::getName()));
    }
    public function testGetResult_exception(): void
    {
        $manager = new AggregationManager();
        $input = [
            [
                "type" => "number",
                "options" => ["A", "B", "C"],
                "answer" => [true, false, false]
            ],
            [
                "type" => "qcm",
                "options" => ["A", "B", "C", "D"],
                "answer" => [true, true, false, true]
            ]
        ];
        $this->expectException(\InvalidArgumentException::class);
        $this->assertNotEmpty($manager->getResult($input, Terms::getName()));
    }
}