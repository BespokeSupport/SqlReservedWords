#!/usr/bin/php -dphar.readonly=0
<?php

$name = 'sql-reserved-words.phar';

$buildRoot = __DIR__;
$phar = new Phar($buildRoot . '/dist/' . $name, 0, $name);
$include = '/^(?=(.*lib|.*vendor)|.*console\.php)(.*)$/i';
$phar->buildFromDirectory($buildRoot, $include);
$phar->setStub("#!/usr/bin/env php\n" . $phar->createDefaultStub("console.php"));
