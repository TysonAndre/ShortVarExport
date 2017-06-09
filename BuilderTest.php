<?php
namespace ShortVarExport;

require_once __DIR__ . '/Builder.php';

/**
 * @author Tyson Andre<tysonandre775@hotmail.com>
 */
class BuilderTest {
    public $assertionsFailed = 0;
    public $assertionsMade = 0;

    public static function run_tests() {
        error_reporting(E_ALL);
        $test = new BuilderTest();
        $test->testBuildMultiLineArray();
        $test->testBuildSingleLineArray();
        printf("Assertions: %d of %d failed\n", $test->assertionsFailed, $test->assertionsMade);
        if ($test->assertionsFailed === 0) {
            echo "All tests passed!\n";
            return 0;
        }
        return 1;
    }

    public function assertSame($x, $y, string $message = '') {
        $this->assertionsMade++;
        if ($x === $y) {
            return;
        }
        $this->assertionsFailed++;
        echo rtrim("Test failed: $message") . "\n";
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        echo "Expected:\n";
        var_dump($x);
        echo "Actual:\n";
        var_dump($y);
        echo "\n";
        return $x;
    }

    public function checkBuildsMultiLineValue(string $expectedRepresentation, $data, string $message = '') {
        $value = Builder::build($data, Builder::MULTI_LINE);
        $this->assertSame($expectedRepresentation, $value, $message);
        $this->assertSame($data, eval("return $expectedRepresentation;"), "Expected the representation to be valid php code, while testing '$message'");
    }

    public function testBuildMultiLineArray() {
        $this->checkBuildsMultiLineValue("2", 2, 'should serialize integers');
        $this->checkBuildsMultiLineValue("-21900", -21900, 'should serialize integers');
        $this->checkBuildsMultiLineValue("2.5", 2.5, 'should serialize floats');
        $this->checkBuildsMultiLineValue("NULL", null, 'should serialize null');
        $this->checkBuildsMultiLineValue("false", false, 'should serialize false');
        $this->checkBuildsMultiLineValue("true", true, 'should serialize true');
        $this->checkBuildsMultiLineValue("[]", [], 'should serialize empty arrays');
        $this->checkBuildsMultiLineValue("[[]]", [[]], 'should serialize empty arrays');
        $this->checkBuildsMultiLineValue("['value']", ["value"], 'should serialize nested single-element arrays');
        $this->checkBuildsMultiLineValue("[[['value']]]", [[['value']]], 'should serialize nested single-element array');
        $this->checkBuildsMultiLineValue("[[[-1=>'value']]]", [[[-1=>"value"]]], 'should serialize single-element array');
        $this->checkBuildsMultiLineValue("[\n'value',\n42,\n4=>'newvalue',\n]", ["value", 42, 4 => "newvalue"], 'should serialize multi-element array');
        $this->checkBuildsMultiLineValue("[\n'value',\n'key'=>'value2',\n]", ["value", "key" => "value2"], 'should serialize multiple element arrays');
        $this->checkBuildsMultiLineValue("['val\\\\\"ue']", ['val\\"ue'], 'should serialize strings with quotes');
        $this->checkBuildsMultiLineValue("['val\"\'\\\\ue']", ['val"\'\\ue'], 'should serialize strings with quotes');
        // should throw for object
    }

    public function checkBuildsSingleLineValue(string $expectedRepresentation, $data, string $message = '') {
        $value = Builder::build($data);
        $this->assertSame($expectedRepresentation, $value, $message);
        $this->assertSame($data, eval("return $expectedRepresentation;"), "Expected the representation to be valid php code, while testing '$message'");
    }

    public function testBuildSingleLineArray() {
        $this->checkBuildsSingleLineValue("2", 2, 'should serialize integers');
        $this->checkBuildsSingleLineValue("-21900", -21900, 'should serialize integers');
        $this->checkBuildsSingleLineValue("2.5", 2.5, 'should serialize floats');
        $this->checkBuildsSingleLineValue("NULL", null, 'should serialize null');
        $this->checkBuildsSingleLineValue("false", false, 'should serialize false');
        $this->checkBuildsSingleLineValue("true", true, 'should serialize true');
        $this->checkBuildsSingleLineValue("[]", [], 'should serialize empty arrays');
        $this->checkBuildsSingleLineValue("[[]]", [[]], 'should serialize empty arrays');
        $this->checkBuildsSingleLineValue("['value']", ["value"], 'should serialize nested single-element arrays');
        $this->checkBuildsSingleLineValue("[[['value']]]", [[['value']]], 'should serialize nested single-element array');
        $this->checkBuildsSingleLineValue("[[[-1=>'value']]]", [[[-1=>"value"]]], 'should serialize single-element array');
        $this->checkBuildsSingleLineValue("['value',42,4=>'newvalue']", ["value", 42, 4 => "newvalue"], 'should serialize multi-element array');
        $this->checkBuildsSingleLineValue("['value','key'=>'value2']", ["value", "key" => "value2"], 'should serialize multiple element arrays');
        $this->checkBuildsSingleLineValue("['val\\\\\"ue']", ['val\\"ue'], 'should serialize strings with quotes');
        $this->checkBuildsSingleLineValue("['val\"\'\\\\ue']", ['val"\'\\ue'], 'should serialize strings with quotes');
        // should throw for object
    }
}

exit(BuilderTest::run_tests());
