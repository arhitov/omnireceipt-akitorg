<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\AkiTorg\Tests;

use Mockery as m;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Strategy\MockClientStrategy;
use Omnireceipt\Common\Contracts\Http\ClientInterface;
use Omnireceipt\Common\Contracts\Http\RequestInterface;
use Omnireceipt\Common\Contracts\Http\ResponseInterface;
use Http\Mock\Client as MockClient;
use Omnireceipt\Common\Http\Client;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class MockTestCase extends TestCase
{
    private ?RequestInterface $mockRequest = null;

    private ?MockClient $mockClient = null;

    private ?ClientInterface $httpClient = null;

    private ?HttpRequest $httpRequest = null;


    protected function setUp(): void
    {
        parent::setUp();

        HttpClientDiscovery::prependStrategy(MockClientStrategy::class);
    }
    public function getMockedRequests()
    {
        return $this->mockClient->getRequests();
    }

    public function getMockHttpResponse($path)
    {
        if ($path instanceof ResponseInterface) {
            return $path;
        }

        return \GuzzleHttp\Psr7\Message::parseResponse(file_get_contents(__DIR__ . '/Mock/' . $path));
    }

    public function setMockHttpResponse($paths)
    {
        foreach ((array) $paths as $path) {
            $this->getMockClient()->addResponse($this->getMockHttpResponse($path));
        }
    }
    public function getMockRequest()
    {
        return $this->mockRequest ??= m::mock(RequestInterface::class);
    }

    public function getMockClient()
    {
        return $this->mockClient ??= new MockClient();
    }

    public function getHttpClient()
    {
        return $this->httpClient ??= new Client(
            $this->getMockClient()
        );
    }

    public function getHttpRequest()
    {
        return $this->httpRequest ??= new HttpRequest;
    }
}
