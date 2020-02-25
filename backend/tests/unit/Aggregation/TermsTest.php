<?php 

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \IWD\JOBINTERVIEW\Aggregation\Terms;

final class TermsTest extends TestCase
{
    public function testSupports(): void
    {
        $validInput = [
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
        $this->assertTrue(Terms::supports($validInput));
        $invalidInput = [
            [
                "type" => "number",
                "options" => ["A", "B", "C"],
                "answer" => null
            ],
            [
                "type" => "qcm",
                "options" => ["A", "B", "C", "D"],
                "answer" => [true, true, false, true]
            ]
        ];
        $this->assertFalse(Terms::supports($invalidInput));
    }
    public function testResult(): void
    {
        $validInput = [
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
        $expected = [
            "A" => 2,
            "B" => 1,
            "C" => 0,
            "D" => 1
        ];
        $actual = Terms::result($validInput);
        $this->assertEquals($expected, $actual);
    }
}