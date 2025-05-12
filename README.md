# RunPod Client for Hyperf

这是一个用于调用 [RunPod API](https://www.runpod.io/) 的 Hyperf 组件，支持常用 Serverless 接口，如异步作业提交、状态查询、取消任务等。

---

## ✨ 功能特性

- `run`：异步提交作业并返回作业 ID
- `runsync`：同步执行作业并直接返回结果
- `status`：查询作业状态及输出
- `stream`：读取流式增量结果
- `cancel`：取消进行中的作业
- `retry`：重试失败或超时的作业
- `purge-queue`：清空队列中的作业
- `health`：检查运行状态

---

## 📦 安装

```bash
composer require zzhenping/runpod-client
```

## 🔧 快速生成配置文件
```bash
php bin/hyperf.php vendor:publish zzhenping/runpod-client
```

## 🧩 使用示例
```
use Zzhenping\RunPodClient\Serverless\ServerlessService;

class IndexController extends AbstractController
{

    public function __construct(protected ServerlessService $runpod){}
    
    public function index()
    {
        return [
            'health' => $this->runpod->health('bcb5un8ejvlce7'),
            'new_health' => $this->runpod->withPool('new_run_pod')->health('m9d0vbz7fzbjum'), // 新的连接
        ];
    }
}


```