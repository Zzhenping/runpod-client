<?php
/**
 * Created by PhpStorm.
 * User: Zhanzhenping
 * Email: stallzhan@gmail.com
 * Date: 2025/5/9
 */

namespace StallZhan\RunPodClient\Contracts;


//IN_QUEUE：该作业正在端点队列中等待可用的工作人员来处理它。
//RUNNING：一名工人已接手该工作并正在积极处理。
//COMPLETED：作业已成功处理完毕并返回结果。
//FAILED：作业在执行过程中遇到错误，未成功完成。
//CANCELLED/cancel/job_id：该作业在完成之前已使用端点手动取消。
//TIMED_OUT：作业在被工作人员拾取之前已过期，或者工作人员在达到超时阈值之前未能报告结果。
class JobStatus
{

    public const PENDING = 'PENDING';
    public const IN_QUEUE = 'IN_QUEUE';
    public const RUNNING = 'RUNNING';
    public const COMPLETED = 'COMPLETED';
    public const FAILED = 'FAILED';
    public const CANCELLED = 'CANCELLED';
    public const TIMED_OUT = 'TIMED_OUT';

}