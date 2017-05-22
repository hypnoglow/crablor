<?php

namespace crablor\Components;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Simple downloader.
 */
class Downloader
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Downloader constructor.
     *
     * @param string $baseURI
     */
    public function __construct(string $baseURI)
    {
        $this->client = new Client([
            'base_uri' => $baseURI
        ]);
    }

    /**
     * @param string $url Page url.
     * @return string Page content.
     * @throws DownloaderException
     */
    public function downloadPage(string $url): string
    {

        try {
            $response = $this->client->request('GET', $url);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $msg = $url . ' responded with ' . $e->getResponse()->getStatusCode();
            } else {
                $msg = $e->getMessage();
            }
            throw new DownloaderException($msg);
        }

        if ($response->getStatusCode() !== 200) {
            throw new DownloaderException($url . ' responded with ' . $response->getStatusCode());
        }

        return (string)$response->getBody();
    }
}
