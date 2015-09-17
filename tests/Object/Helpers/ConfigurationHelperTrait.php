<?php
namespace SimplyAdmire\Zaaksysteem\Tests\Unit\Object\Helpers;

/**
 * Trait containing helper functions for handling Configuration objects
 * during test runs
 */
trait ConfigurationHelperTrait
{

    /**
     * Merges a configuration array with the minimal set of required configuration
     *
     * @param array $configuration
     * @return array
     */
    private function mergeConfigurationWithMinimalConfiguration(array $configuration = [])
    {
        return array_merge(
            [
                'apiBaseUrl' => 'http://foobar.com',
                'username' => 'foo bar',
                'apiKey' => 'api key'
            ],
            $configuration
        );
    }

}