<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Zzhenping\RunPodClient;

use Zzhenping\RunPodClient\Serverless\ServerlessInterface;
use Zzhenping\RunPodClient\Serverless\ServerlessService;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                ServerlessInterface::class => ServerlessService::class,
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config of runpod client.',
                    'source' => __DIR__ . '/../publish/runpod.php',
                    'destination' => BASE_PATH . '/config/autoload/runpod.php',
                ],
            ],
        ];
    }
}
