<?php
/**
 * Created by PhpStorm.
 * User: Zhanzhenping
 * Email: stallzhan@gmail.com
 * Date: 2025/5/9
 */

namespace Zzhenping\RunPodClient\Serverless;

interface ServerlessInterface
{

    public function run(string $endpointId, array $payload): array;

    public function runSync(string $endpointId, array $payload): array;

    public function status(string $endpointId, string $jobId): array;

    public function cancel(string $endpointId, string $jobId): bool;

    public function retry(string $endpointId, string $jobId): bool;

    public function purgeQueue(string $endpointId): array;

    public function health(string $endpointId): array;

}