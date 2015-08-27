<?php
namespace SimplyAdmire\Zaaksysteem;

use Assert\Assertion;
use Assert\InvalidArgumentException;

/**
 * This class contains the configuration of an API client object.
 *
 * The constructor accepts an array with configuration, from which the following settings are required:
 *
 * - apiBaseUrl (also validated for being a valid url)
 *
 * Optional settings:
 *
 * - apiVersion (validated on being a valid API version, now only 1 exists)
 * - clientConfiguration (an array that is passed as configuration to the guzzle client)
 */
final class Configuration
{

    /**
     * The version of the API to use
     *
     * @var integer
     */
    private $apiVersion = 1;

    /**
     * The base URI of the API
     *
     * @var string
     */
    private $apiBaseUrl;

    /**
     * A possible configuration array for the guzzle client
     *
     * @var array
     */
    private $clientConfiguration = [];

    /**
     * The API key configured in Zaaksysteem
     *
     * @var string
     */
    private $apiKey;

    /**
     * The username configured in Zaaksysteem
     *
     * @var string
     */
    private $username;

    /**
     * @param array $configuration Array with configuration settings
     * @throws InvalidArgumentException
     */
    public function __construct(array $configuration)
    {
        // Validate and set base url
        Assertion::notEmptyKey($configuration, 'apiBaseUrl', 'apiBaseUrl is required');
        Assertion::url($configuration['apiBaseUrl'], 'apiBaseUrl has to be a valid url');
        $this->apiBaseUrl = rtrim($configuration['apiBaseUrl'], '/');

        // Validate username
        Assertion::notEmptyKey($configuration, 'username', 'username is required');
        Assertion::string($configuration['username'], 'username has to be a string');
        $this->username = trim($configuration['username']);

        // Validate api key
        Assertion::notEmptyKey($configuration, 'apiKey', 'apiKey is required');
        Assertion::string($configuration['apiKey'], 'apiKey has to be a string');
        $this->apiKey = trim($configuration['apiKey']);

        // Check if apiVersion is set and valid
        if (isset($configuration['apiVersion'])) {
            Assertion::integer($configuration['apiVersion'], 'apiVersion has to be an integer');
            Assertion::inArray($configuration['apiVersion'], [1], 'Invalid apiVersion');
            $this->apiVersion = (integer)$configuration['apiVersion'];
        }

        // Check if clientConfiguration is set and valid
        if (isset($configuration['clientConfiguration'])) {
            Assertion::isArray($configuration['clientConfiguration'], 'clientConfiguration has to be an array');
            $this->clientConfiguration = $configuration['clientConfiguration'];
        }
    }

    /**
     * @return array
     */
    public function getClientConfiguration()
    {
        return $this->clientConfiguration;
    }

    /**
     * @return string
     */
    public function getApiBaseUrl()
    {
        return sprintf('%s/v%s/', $this->apiBaseUrl, $this->apiVersion);
    }

}