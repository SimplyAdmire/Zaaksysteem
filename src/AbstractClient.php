<?php
namespace SimplyAdmire\Zaaksysteem;

use Assert\Assertion;
use Assert\InvalidArgumentException;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use SimplyAdmire\Zaaksysteem\Exception\RequestException;
use SimplyAdmire\Zaaksysteem\Exception\ResponseException;

abstract class AbstractClient
{

    /**
     * Configuration object containing the API configuration
     *
     * @var Configuration
     */
    protected $configuration;

    /**
     * A guzzle HTTP client
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @param Configuration $configuration
     * @param HttpClientInterface|null $client
     */
    public function __construct(Configuration $configuration, HttpClientInterface $client = null)
    {
        $this->configuration = $configuration;

        if ($client === null) {
            $this->client = new \GuzzleHttp\Client($this->configuration->getClientConfiguration());
        } else {
            $this->client = $client;
        }
    }

    /**
     * @param array $options
     * @return array
     */
    protected function buildRequestConfiguration(array $options = [])
    {
        // Set required options
        $options['auth'] = [$this->configuration->getUsername(), $this->configuration->getApiKey(), 'digest'];
        $options['headers'] = array_merge(
            isset($options['headers']) ? $options['headers'] : [],
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        );

        // Some default settings that can be overwritten by feeding the configuration object with a client configuration
        $options = array_merge(
            [
                'connect_timeout' => 2
            ],
            $options
        );

        return $options;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function buildRequestUrl($path) {
        return sprintf('%s%s', $this->configuration->getApiBaseUrl(), $path);
    }

    /**
     * @param string $requestMethod
     * @param string $path
     * @param array $options
     * @return array
     * @throws RequestException If the request fails
     * @throws ResponseException If the response does not contain valid json / the expected structure
     */
    public function request($requestMethod, $path, array $options = [])
    {
        $url = $this->buildRequestUrl($path);
        $options = $this->buildRequestConfiguration($options);

        try {
            $response = $this->client->request($requestMethod, $url, $options);
            if (!$response instanceof Response) {
                throw new \Exception('An error occurred while calling the API');
            }
            $content = $response->getBody()->getContents();

            Assertion::isJsonString($content);
            $content = json_decode($content, true);

            return $this->getResult($content);
        } catch (GuzzleException $exception) {
            throw new RequestException('An error occurred while calling the API');
        } catch (InvalidArgumentException $exception) {
            throw new ResponseException('The response did not contain valid JSON');
        } catch (\Exception $exception) {
            throw new RequestException('An error occurred while calling the API');
        }
    }

    /**
     * @param string $path
     * @param array $options
     * @return string
     * @throws RequestException
     */
    public function download($path, array $options = []) {
        $url = $this->buildRequestUrl($path);
        $options = $this->buildRequestConfiguration($options);

        try {
            $response = $this->client->request('GET', $url, $options);
            if (!$response instanceof Response) {
                throw new \Exception('An error occurred while calling the API');
            }
            return $response->getBody()->getContents();
        } catch (\Exception $exception) {
            throw new RequestException('An error occurred while calling the API');
        }
    }

    /**
     * Returns the result from the total response content
     *
     * @param array $content
     * @return array
     */
    abstract protected function getResult(array $content);

}