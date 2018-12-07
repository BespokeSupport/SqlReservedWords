<?php

class SqlReservedCheckerTest extends \PHPUnit\Framework\TestCase
{
    private const FILE_ERRORS = \Example\Group::class;
    private const FILE_OK = \Example\NoErrors::class;

    private function keywordsAll()
    {
        return \BespokeSupport\SqlReserved\SqlReservedChecker::keywords();
    }

    private function keywordsMysql()
    {
        return \BespokeSupport\SqlReserved\SqlReservedChecker::keywords('mysql');
    }

    /**
     * @expectedException TypeError
     */
    public function testReflectionNull()
    {
        \BespokeSupport\SqlReserved\SqlReservedChecker::reflection(null, []);
    }

    public function testReflectionEmptyKeywords()
    {
        $ref = new \ReflectionClass(self::class);
        $errors = \BespokeSupport\SqlReserved\SqlReservedChecker::reflection($ref, []);

        $this->assertCount(1, $errors);
        $this->assertEquals(\BespokeSupport\SqlReserved\SqlReservedException::ERROR_NO_KEYWORDS, $errors[0]);
    }

    public function testReflectionKeywords()
    {
        $ref = new \ReflectionClass(self::class);
        $errors = \BespokeSupport\SqlReserved\SqlReservedChecker::reflection($ref, self::keywordsAll());

        $this->assertCount(0, $errors);
    }

    public function testReflectionAll()
    {
        $ref = new \ReflectionClass(self::FILE_ERRORS);
        $errors = \BespokeSupport\SqlReserved\SqlReservedChecker::reflection($ref, self::keywordsAll());

        $this->assertCount(2, $errors);

        $this->assertRegExp('#Group#', $errors[0]);
        $this->assertRegExp('#user#', $errors[1]);
    }

    public function testReflectionMySQL()
    {
        $ref = new \ReflectionClass(self::FILE_ERRORS);
        $errors = \BespokeSupport\SqlReserved\SqlReservedChecker::reflection($ref, self::keywordsMysql());

        $this->assertCount(1, $errors);
        $this->assertRegExp('#Group#', $errors[0]);
    }

    public function testReflectionSingle()
    {
        $ref = new \ReflectionClass(self::FILE_ERRORS);
        $errors = \BespokeSupport\SqlReserved\SqlReservedChecker::reflection($ref, \BespokeSupport\SqlReserved\SqlReservedChecker::keywords('mysql80'));

        $this->assertCount(1, $errors);
        $this->assertRegExp('#Group#', $errors[0]);
    }

    public function testCheck()
    {
        $errors = \BespokeSupport\SqlReserved\SqlReservedChecker::checkClassName(self::FILE_OK, self::keywordsAll());
        $this->assertCount(0, $errors);
    }

    public function testCheckClassName()
    {
        $errors = \BespokeSupport\SqlReserved\SqlReservedChecker::checkClassName(self::FILE_ERRORS, self::keywordsMysql());
        $this->assertCount(1, $errors);
        $this->assertRegExp('#Group#', $errors[0]);
    }

    public function testCheckClassNameUnknown()
    {
        $errors = \BespokeSupport\SqlReserved\SqlReservedChecker::checkClassName('', []);
        $this->assertCount(1, $errors);
        $this->assertRegExp('#Failed to load#', $errors[0]);
    }
}
