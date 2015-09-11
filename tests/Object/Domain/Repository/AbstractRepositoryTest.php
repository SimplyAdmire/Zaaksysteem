<?php
namespace SimplyAdmire\Zaaksysteem\Tests\Unit\Object\Domain\Repository;

use SimplyAdmire\Zaaksysteem\Tests\Unit\Helpers\ConfigurationHelperTrait;
use SimplyAdmire\Zaaksysteem\V1\Client;

require_once(__DIR__ . '/../../../V1/Helpers/ConfigurationHelperTrait.php');

abstract class AbstractRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationHelperTrait;

    /**
     * @return string
     */
    abstract protected function getListFixturePath();

    /**
     * @return string
     */
    abstract protected function getDetailFixturePath();

    /**
     * @var string
     */
    protected $repositoryClassName;

    /**
     * @var string
     */
    protected $modelClassName;

    /**
     * @test
     */
    public function findOneByIdentifierReturnsModel()
    {
        $identifier = '0123456789-abcdef';
        $response = json_decode(file_get_contents($this->getDetailFixturePath()), true);
        $response['result'][0]['id'] = $identifier;

        $mockClient = $this->getMock(Client::class, ['request'], [], '', false);
        $mockClient->expects($this->any())
            ->method('request')
            ->will($this->returnValue($response['result'][0]));

        $repository = new $this->repositoryClassName($mockClient);

        $result = $repository->findOneByIdentifier($identifier);
        $this->assertInstanceOf($this->modelClassName, $result);
        $this->assertEquals($identifier, $result->getId());
    }
}