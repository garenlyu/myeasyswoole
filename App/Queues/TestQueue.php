<?php

namespace App\Queues;

use EasySwoole\Queue\Queue;
use EasySwoole\Component\Singleton;

class TestQueue extends Queue
{
    use Singleton;
}