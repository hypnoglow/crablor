<?php

namespace crablor\Components;

use function SimpleHtmlDom\str_get_html;

/**
 * Refs parser.
 */
class RefParser
{
    /**
     * @var string
     */
    protected $host;

    /**
     * RefParser constructor.
     *
     * @param string $host
     * @param Logger $logger
     */
    public function __construct(string $host, ?Logger $logger)
    {
        $this->host = $host;
        $this->logger = $logger;
    }

    /**
     * @param string $page
     * @return string[]
     */
    public function getAllPaths(string $page): array
    {
        $refs = $this->getAllRefs($page);
        $paths = $this->extractPaths($refs);
        return $paths;
    }

    /**
     * @param string $page Page content.
     * @return string[] Array of refs.
     */
    protected function getAllRefs(string $page): array
    {
        $refs = [];

        $html = str_get_html($page);

        if ($html === false) {
            return [];
        }

        foreach ($html->find('a') as $ref) {
            $href = $ref->href;
            if ($href === null) {
                continue;
            }

            $refs[] = $href;
        }

        return array_filter(array_unique($refs));
    }

    /**
     * @param string[] $refs
     * @return string[]
     */
    protected function extractPaths(array $refs): array
    {
        $paths = [];

        foreach ($refs as $ref) {
            $paths[] = $this->extractPath($ref);
        }

        return array_filter(array_unique($paths));
    }

    /**
     * @param string $ref
     * @return null|string
     */
    protected function extractPath(string $ref): ?string
    {
        $components = parse_url($ref);

        // Filter other schemes, e.g. "mailto".
        $scheme = $components['scheme'] ?? '';
        if ($scheme && !preg_match('#https?#', $scheme)) {
            $this->log(sprintf('Ref %s has unsuitable scheme.', $ref));
            return null;
        }

        // Filter external refs.
        $host = $components['host'] ?? '';
        $host = str_replace('www.', '', $host);
        if ($host && $host !== $this->host) {
            $this->log(sprintf('Ref %s has external host.', $ref));
            return null;
        }

        $path = $components['path'] ?? null;
        if (!$path) {
            $this->log(sprintf('Ref %s has no path.', $ref));
            return null;
        }

        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        return $path;
    }

    /**
     * @param $message
     */
    protected function log($message): void
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->debug($message);
    }
}
