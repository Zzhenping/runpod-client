<?php
/**
 * Created by PhpStorm.
 * User: Zhanzhenping
 * Email: stallzhan@gmail.com
 * Date: 2025/5/9
 */

namespace HyperfTest\Cases;

use Zzhenping\RunPodClient\RunPodClient;
use Zzhenping\RunPodClient\Serverless\ServerlessService;
use GuzzleHttp\Client;
use function Hyperf\Coroutine\go;

class ServerlessServiceTest extends AbstractTestCase
{
    protected ServerlessService $service;
    protected string $endpointId = 'bcb5un8ejvlce7';
    protected array $testPayload = [
        'input' => [
            'text' => '人间灯火倒映湖中，她的渴望让静水泛起涟漪。若代价只是孤独，那就让这份愿望肆意流淌。流入她所注视的世间，也流入她如湖水般澄澈的目光。',
            'reference_audio' => 'https://da1mnv1i99sez.cloudfront.net/team-media/6528/audio/771_0db6d2c7ef4326a92ffedf5f644c9c36e565bf848acea3a3c7eb30fce8889678.wav',
            'has_enable_audio_to_text' => true
        ]
    ];

    protected function setUp(): void
    {
        parent::setUp();
        // 加载 .env 文件
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    public function testServerlessHealth()
    {
        $response = $this->service->health($this->endpointId);
        # 断言 Response 中存在 jobs 字段和  workers 字段
        $this->assertArrayHasKey('jobs', $response);
        $this->assertArrayHasKey('workers', $response);
    }

    public function testRetryJob()
    {
        // 仅适用于状态为 FAILED 或 TIMED_OUT 的作业
        $result = $this->service->retry('ddu5oy0u9lpicg', "ffb30ceb-e838-4948-994a-06020698dc79-u2");
        $this->assertTrue($result);
    }

    public function testCancelJob()
    {
        $result = $this->service->cancel($this->endpointId, "4f5690cc-4832-4838-98e0-25ebaef7cdc6-e1");
        $this->assertTrue($result);
    }

    public function testRunReturnsJobId()
    {
        $response = $this->service->run($this->endpointId, $this->testPayload);
        # 交给协程来检查任务状态
        go(function () use (&$response) {
            $jobId = $response['id'];
            while (true) {
                $statusResponse = $this->service->status($this->endpointId, $jobId);
                var_dump($statusResponse['status']);
                if (in_array($statusResponse['status'], ['COMPLETED', 'FAILED', 'CANCELLED', 'TIMED_OUT'])) {
                    echo "任务已结束，状态：{$statusResponse['status']}\n";
                    break;
                }
                \Swoole\Coroutine::sleep(2);
            }
        });

        sleep(2);
        $this->service->cancel($this->endpointId, $response['id']);

        $this->assertIsString($response['id']);
        $this->assertEquals('IN_QUEUE', $response['status']);
    }

    public function testRunSyncReturnsJobId()
    {
        // 为同步请求设置不同的超时时间
        $response = $this->service->runSync($this->endpointId, $this->testPayload);
        $this->assertEquals('COMPLETED', $response['status']);
    }
}