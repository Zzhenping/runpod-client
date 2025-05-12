<?php
/**
 * Created by PhpStorm.
 * User: Zhanzhenping
 * Email: stallzhan@gmail.com
 * Date: 2025/5/9
 */

namespace Zzhenping\RunPodClient\Serverless;

use Zzhenping\RunPodClient\Contracts\JobStatus;
use Zzhenping\RunPodClient\RunPodClient;

class ServerlessService implements ServerlessInterface
{

    public function __construct(protected RunPodClient $client) {}

    public function withPool(string $poolName): self
    {
        $newClient = new RunPodClient($this->client->getConfig(), $poolName);
        return new self($newClient);
    }

    public function run(string $endpointId, array $payload): array
    {
        // TODO: Implement run() method.
        return $this->client->post("/v2/{$endpointId}/run", $payload);
    }

    public function runSync(string $endpointId, array $payload): array
    {
        // TODO: Implement runSync() method.
        return $this->client->post("/v2/{$endpointId}/runsync", $payload);
    }

    public function status(string $endpointId, string $jobId): array
    {
        // TODO: Implement status() method.
        return $this->client->get("/v2/{$endpointId}/status/{$jobId}");
    }

    public function cancel(string $endpointId, string $jobId): bool
    {
        // TODO: Implement cancel() method.
        $response = $this->client->post("/v2/{$endpointId}/cancel/{$jobId}", []);
        if ((isset($response['status']) && $response['status'] === JobStatus::COMPLETED)) {
            return true;
        }
        $response = $this->status($endpointId, $jobId);
        if (isset($response['status']) && $response['status'] === JobStatus::CANCELLED) {
            return true;
        }
        return false;
    }

    public function retry(string $endpointId, string $jobId): bool
    {
        $response = $this->status($endpointId, $jobId);
        // 仅适用于状态为 FAILED 或 TIMED_OUT 的作业
        if (!isset($response['status']) || !in_array($response['status'], [JobStatus::FAILED, JobStatus::TIMED_OUT])) {
            return false;
        }
        // TODO: Implement retry() method.
        $this->client->post("/v2/{$endpointId}/retry/{$jobId}", []);
        return true;
    }

    public function purgeQueue(string $endpointId): array
    {
        // TODO: Implement purgeQueue() method.
        return $this->client->post("/v2/{$endpointId}/purge-queue", []);
    }

    public function health(string $endpointId): array
    {
        // TODO: Implement health() method.
        return $this->client->get("/v2/{$endpointId}/health");
    }
}