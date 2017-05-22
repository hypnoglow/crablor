<?php

namespace crablor;

use crablor\Components\Downloader;
use crablor\Components\DownloaderException;
use crablor\Components\RefParser;
use crablor\Components\Logger;

/**
 * Simple crawler.
 */
class Crawler
{
    /**
     * @var Downloader
     */
    protected $downloader;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var RefParser
     */
    protected $parser;

    /**
     * @var string[]
     */
    protected $sitemap = [];

    /**
     * Runs the crawler.
     *
     * @param array $arguments Parsed docopt arguments.
     */
    public function run(array $arguments)
    {
        // Process arguments.
        $targetURL = (string)$arguments['<target_url>'];
        $sleepMS = $arguments['--sleepMS'] ?? 1000;
        $sleepMS = (int)$sleepMS;
        $outfile = $arguments['--out'] ?? null;
        $quiet = (bool)$arguments['--quiet'];
        $verbose = (bool)$arguments['--verbose'];

        // Execute the logic.
        $this->init($targetURL, $outfile, $quiet, $verbose);
        $this->parse('/', $sleepMS);
    }

    /**
     * @param string $targetURL
     * @param string|null $outfile
     * @param bool $quiet
     * @param bool $verbose
     */
    protected function init(string $targetURL, ?string $outfile, bool $quiet, bool $verbose): void
    {
        $host = parse_url($targetURL, PHP_URL_HOST);
        $scheme = parse_url($targetURL, PHP_URL_SCHEME);

        if ($outfile) {
            $outfile = fopen($outfile, 'w');
        }

        $logLevel = Logger::LEVEL_NORMAL;
        if ($verbose) {
            $logLevel = Logger::LEVEL_VERBOSE;
        }
        if ($quiet) {
            $logLevel = Logger::LEVEL_QUIET;
        }

        $this->logger = new Logger($logLevel, $outfile, null);
        $this->downloader = new Downloader(sprintf('%s://%s', $scheme, $host));
        $this->parser = new RefParser($host, $this->logger);
    }

    /**
     * @param string $url
     * @param int $sleepMS
     */
    protected function parse(string $url, int $sleepMS = 1000): void
    {
        // Do not parse same page twice.
        if (array_key_exists($url, $this->sitemap)) {
            return;
        }

        $this->logger->info('Downloading: ' . $url . ' ... ', false);
        try {
            $page = $this->downloader->downloadPage($url);
        } catch (DownloaderException $e) {
            $this->logger->continueInfo('Fail.');
            $this->logger->error($e->getMessage());
            $this->sitemap[$url] = false;
            return;
        } finally {
            // Sleep after downloading the page.
            usleep($sleepMS * 1000);
        }

        $this->logger->continueInfo('OK.');
        $this->logger->message($url);
        $this->sitemap[$url] = true;

        // Recursively parse all refs.
        $paths = $this->parser-> getAllPaths($page);
        if (empty($paths)) {
            $this->logger->debug('Page has no paths.');
            return;
        }

        foreach ($paths as $path) {
            $this->parse($path, $sleepMS);
        }
    }
}
