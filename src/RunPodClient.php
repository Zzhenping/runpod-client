<?php

namespace Zzhenping\RunPodClient;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Zzhenping\RunPodClient\Contracts\StatusCode;
use Zzhenping\RunPodClient\Exceptions\NotFoundException;
use Zzhenping\RunPodClient\Exceptions\ServerErrorException;
use Zzhenping\RunPodClient\Exceptions\UnauthorizedException;
use Zzhenping\RunPodClient\Exceptions\BadRequestException;
use Zzhenping\RunPodClient\Exceptions\TimeoutErrorException;
use Hyperf\Contract\ConfigInterface;

class RunPodClient
{
    protected Client $client;

    protected ConfigInterface $config;

    public function __construct(ConfigInterface $config,  protected string $poolName = 'default') {
        $this->config = $config;
        $conf = $config->get(sprintf('runpod.%s', $poolName), false);
        if (!$conf) {
            throw new \InvalidArgumentException(sprintf(
                'RunPod configuration for pool "%s" not found',
                $poolName
            ));
        }
        $this->client = new Client([
            'base_uri' => $conf['base_uri'],
            'headers' => [
                'Authorization' => $conf['token'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => $conf['timeout'],
        ]);
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * throws UnauthorizedException
     * throws ServerErrorException
     * throws BadRequestException
     * throws TimeoutErrorException
     * throws NotFoundException
     */
    public function get(string $uri): array
    {
        return $this->request('GET', $uri);
    }

    /**
     * throws UnauthorizedException
     * throws ServerErrorException
     * throws BadRequestException
     * throws TimeoutErrorException
     * throws NotFoundException
     */
    public function post(string $uri, array $data): array
    {
        return $this->request('POST', $uri, ['json' => $data]);
    }

    protected function request(string $method, string $uri, array $options = []): array
    {
        try {
            # 获取请求头
            $headers = $this->client->getConfig('headers');
            $response = $this->client->request($method, $uri, $options);
            return $this->decodeResponse($response);
        } catch (ClientException $e) {
            $this->handleClientException($e);
        } catch (ConnectException $e) {
            throw new TimeoutErrorException('Timeout Error: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (GuzzleException $e) {
            throw new ServerErrorException('Server Error: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function decodeResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    protected function handleClientException(ClientException $e): void
    {
        $statusCode = $e->getResponse()->getStatusCode();
        throw match ($statusCode) {
            StatusCode::BAD_REQUEST => new BadRequestException('Bad Request: ' . $e->getMessage(), $statusCode, $e),
            StatusCode::UNAUTHORIZED => new UnauthorizedException('Unauthorized: ' . $e->getMessage(), $statusCode, $e),
            StatusCode::NOT_FOUND => new NotFoundException('Not Found: ' . $e->getMessage(), $statusCode, $e),
            default => new ServerErrorException('Server Error: ' . $e->getMessage(), $statusCode, $e),
        };
    }
}
