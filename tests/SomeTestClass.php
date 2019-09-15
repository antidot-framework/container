<?php


namespace AntidotTest\Container;

class SomeTestClass
{
    private $stack;
    private $queue;
    /**
     * @var \SplObjectStorage
     */
    private $storage;

    public function __construct(
        \SplStack $stack,
        \SplQueue $queue,
        \SplObjectStorage $storage,
        array $foo = [],
        string $bar = 'foo',
        array $bazz = []
    ) {
        $this->stack = $stack;
        $this->queue = $queue;
        $this->storage = $storage;
    }

    public function __invoke(): void
    {
        $this->stack->toArray();
        $this->queue->count();
    }

    public function getQueue(): \SplQueue
    {
        return $this->queue;
    }

    public function getStack(): \SplStack
    {
        return $this->stack;
    }
}
