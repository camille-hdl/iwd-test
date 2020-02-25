<?php 

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \IWD\JOBINTERVIEW\Aggregation\Average;

final class AverageTest extends TestCase
{
    public function testSupports(): void
    {
        $validInput = [
            [
                "type" => "number",
                "answer" => 0
            ],
            [
                "type" => "number",
                "answer" => 10
            ]
        ];
        $this->assertTrue(Average::supports($validInput));
        $invalidInput = [
            [
                "type" => "number",
                "options" => ["A", "B", "C"],
                "answer" => 1
            ],
            [
                "type" => "qcm",
                "options" => ["A", "B", "C", "D"],
                "answer" => [true, true, false, true]
            ]
        ];
        $this->assertFalse(Average::supports($invalidInput));
    }
    public function testResult(): void
    {
        $validInput = [
            [
                "type" => "number",
                "answer" => 10
            ],
            [
                "type" => "number",
                "answer" => 20
            ]
        ];
        $expected = 15.00;
        $actual = Average::result($validInput);
        $this->assertEquals($expected, $actual);
        $this->assertNull(Average::result([]));
    }
}