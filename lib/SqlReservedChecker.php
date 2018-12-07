<?php

namespace BespokeSupport\SqlReserved;

use Doctrine\DBAL\Platforms\Keywords\KeywordList;
use Doctrine\DBAL\Platforms\Keywords\DB2Keywords;
use Doctrine\DBAL\Platforms\Keywords\MySQL57Keywords;
use Doctrine\DBAL\Platforms\Keywords\MySQL80Keywords;
use Doctrine\DBAL\Platforms\Keywords\MySQLKeywords;
use Doctrine\DBAL\Platforms\Keywords\OracleKeywords;
use Doctrine\DBAL\Platforms\Keywords\PostgreSQL100Keywords;
use Doctrine\DBAL\Platforms\Keywords\PostgreSQL91Keywords;
use Doctrine\DBAL\Platforms\Keywords\PostgreSQL92Keywords;
use Doctrine\DBAL\Platforms\Keywords\PostgreSQL94Keywords;
use Doctrine\DBAL\Platforms\Keywords\SQLAnywhere11Keywords;
use Doctrine\DBAL\Platforms\Keywords\SQLAnywhere12Keywords;
use Doctrine\DBAL\Platforms\Keywords\SQLAnywhere16Keywords;
use Doctrine\DBAL\Platforms\Keywords\SQLAnywhereKeywords;
use Doctrine\DBAL\Platforms\Keywords\SQLiteKeywords;
use Doctrine\DBAL\Platforms\Keywords\SQLServer2005Keywords;
use Doctrine\DBAL\Platforms\Keywords\SQLServer2008Keywords;
use Doctrine\DBAL\Platforms\Keywords\SQLServer2012Keywords;
use Doctrine\DBAL\Platforms\Keywords\SQLServerKeywords;
/**
 * Class SqlReservedChecker
 * @package BespokeSupport\SqlReserved
 */
class SqlReservedChecker
{
    /**
     * @var string[]
     */
    public static $keywordListClasses = [
        'db2' => DB2Keywords::class,
        'mysql55' => MySQLKeywords::class,
        'mysql57' => MySQL57Keywords::class,
        'mysql80' => MySQL80Keywords::class,
        'sqlserver2000' => SQLServerKeywords::class,
        'sqlserver2005' => SQLServer2005Keywords::class,
        'sqlserver2008' => SQLServer2008Keywords::class,
        'sqlserver2012' => SQLServer2012Keywords::class,
        'sqlite' => SQLiteKeywords::class,
        'pgsql91' => PostgreSQL91Keywords::class,
        'pgsql92' => PostgreSQL92Keywords::class,
        'pgsql94' => PostgreSQL94Keywords::class,
        'pgsql10' => PostgreSQL100Keywords::class,
        'oracle' => OracleKeywords::class,
        'sqlanywhere10' => SQLAnywhereKeywords::class,
        'sqlanywhere11' => SQLAnywhere11Keywords::class,
        'sqlanywhere12' => SQLAnywhere12Keywords::class,
        'sqlanywhere16' => SQLAnywhere16Keywords::class,
    ];

    public static $groups = ['mysql', 'pgsql', 'db2', 'oracle', 'sqlserver', 'sqlite', 'sqlanywhere'];

    /**
     * @param $search
     * @return array
     */
    public static function filterList($search)
    {
        $dbList = self::filter($search);

        $found = $dbList ? array_intersect($dbList, SqlReservedChecker::$groups) : SqlReservedChecker::$groups;

        return $found;
    }

    /**
     * @param $databases
     * @return string[]
     */
    private static function filter($databases): array
    {
        if (!$databases) {
            return [];
        }

        return explode(',', $databases);
    }

    /**
     * @param $filter
     * @return KeywordList[]
     */
    public static function keywords($filter = null): array
    {
        if (array_key_exists($filter, self::$keywordListClasses)) {
            $class = self::$keywordListClasses[$filter];
            return [$filter => new $class()];
        }

        $filtered = self::filterList($filter);

        $keywords = [];

        foreach (SqlReservedChecker::$keywordListClasses as $name => $class) {
            $search = preg_replace('#\d#', '', $name);
            $match = preg_grep("#^$search#", $filtered);

            if (!$match) {
                continue;
            }

            $keywords[$name] = new $class();
        }

        return $keywords;
    }

    /**
     * @param string $fullClassName
     * @param KeywordList[] $keywords
     * @return array
     */
    public static function checkClassName(string $fullClassName, array $keywords)
    {
        try {
            $ref = new \ReflectionClass($fullClassName);

            return SqlReservedChecker::reflection($ref, $keywords);
        } catch (\Exception $e) {
            return ["Failed to load $fullClassName"];
        }
    }

    /**
     * @param \ReflectionClass $ref
     * @param KeywordList[] $keywords
     * @return string[]
     */
    public static function reflection(\ReflectionClass $ref, array $keywords)
    {
        $errors = [];

        if (!count($keywords)) {
            return [SqlReservedException::ERROR_NO_KEYWORDS];
        }

        if ($keyword = self::check($ref->getShortName(), $keywords)) {
            $errors[] = "'{$ref->getShortName()}' class name is a keyword for '{$keyword}'";
        }

        foreach ($ref->getProperties() as $property) {
            if ($keyword = self::check($property->name, $keywords)) {
                $errors[] = "'{$ref->getShortName()}'::`{$property->name}` is a keyword for '{$keyword}";
            }
        }

        return $errors;
    }

    /**
     * @param string $word
     * @param KeywordList[] $keywords
     * @return string
     */
    public static function check(string $word, array $keywords)
    {
        $words = '';
        foreach ($keywords as $name => $keyword) {
            if ($keyword->isKeyword($word)) {
                $words .= $name . ',';
            }
        }

        return rtrim($words, ',');
    }
}
