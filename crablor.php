#!/usr/bin/env php
<?php

/* ---------- ---------- ---------- ---------- */

// TODO: this should be configured through php.ini

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');
ini_set('memory_limit', '1024M');
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('log_errors', false);

/* ---------- ---------- ---------- ---------- */

$rootDir = __DIR__;
require_once $rootDir . '/vendor/autoload.php';

/* ---------- ---------- ---------- ---------- */

$doc = <<<DOC
crablor - simple crawler.
     
Usage:
  crablor.php <target_url>
  crablor.php [--sleepMS=<sleep>] [--out=<filename>] [--quiet|--verbose] <target_url>
  crablor.php --help
  crablor.php --version

Options:
  -h --help          Show this screen.
  --version          Show version.
  --sleepMS=<sleep>  Sleep between requests in milliseconds.
  --out=<filename>   Output crawled URLs to filename instead of stdout.
  -q --quiet         Quiet mode. Do not output any info and debug messages.
  -v --verbose       Verbose mode. Output debug messages.
    
DOC;

$parsedArgs = \Docopt::handle($doc, ['argv' => array_slice($argv, 1), 'version' => 'crablor beta'])->args;

$cli = new \crablor\Crawler();
$cli->run($parsedArgs);
exit(0);
