<?php

namespace Dant89\SmiteApiClient;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class AbstractHttpClient
 * @package Dant89\SmiteApiClient
 */
class AbstractHttpClient
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * IndexController constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->httpClient = HttpClient::create([
            'headers' => [
                'User-Agent' => 'SMITE-API-PHP-CLIENT/1.0.0',
            ]
        ]);
    }

    /**
     * @param string $uri
     * @param array $options
     * @return Response
     */
    public function get(string $uri, array $options = []): Response
    {
        $response = new Response();
        $url = $this->client->getBaseUrl() . $uri;

        try {
            $httpResponse = $this->httpClient->request('GET', $url, $options);
            $response->setStatus($httpResponse->getStatusCode());
            $response->setHeaders($httpResponse->getHeaders(false));
            $response->setContent($httpResponse->toArray(false));
        } catch (DecodingExceptionInterface |
        RedirectionExceptionInterface |
        ClientExceptionInterface |
        ServerExceptionInterface |
        TransportExceptionInterface $e
        ) {
            $response->setStatus(500);
            $response->setContent([
                'title' => 'Client Error',
                'message' => $e->getMessage()
            ]);
        }

        return $response;
    }
}
