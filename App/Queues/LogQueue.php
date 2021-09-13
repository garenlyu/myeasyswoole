<?php

namespace App\Queues;

use EasySwoole\Queue\Queue;
use EasySwoole\Component\Singleton;

class LogQueue extends Queue
{
    use Singleton;
}