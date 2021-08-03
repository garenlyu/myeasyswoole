<?php

namespace App\Queues;

use EasySwoole\Queue\Queue;
use EasySwoole\Component\CoroutineSingleTon;

class TestQueue extends Queue
{
    use CoroutineSingleTon;
}