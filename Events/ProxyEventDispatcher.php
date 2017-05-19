<?php

namespace Enqueue\Bundle\Events;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProxyEventDispatcher extends ContainerAwareEventDispatcher
{
    /**
     * @var EventDispatcherInterface
     */
    private $trueEventDispatcher;

    /**
     * @var AsyncListener
     */
    private $asyncListener;

    /**
     * @param ContainerInterface       $container
     * @param EventDispatcherInterface $trueEventDispatcher
     * @param AsyncListener            $asyncListener
     */
    public function __construct(ContainerInterface $container, EventDispatcherInterface $trueEventDispatcher, AsyncListener $asyncListener)
    {
        parent::__construct($container);

        $this->trueEventDispatcher = $trueEventDispatcher;
        $this->asyncListener = $asyncListener;
    }

    /**
     * This method dispatches only those listeners that were marked as async.
     *
     * @param string     $eventName
     * @param Event|null $event
     */
    public function dispatchAsyncListenersOnly($eventName, Event $event = null)
    {
        $this->asyncListener->resetSyncMode();
        $this->asyncListener->syncMode($eventName);

        parent::dispatch($eventName, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        parent::dispatch($eventName, $event);

        $this->trueEventDispatcher->dispatch($eventName, $event);
    }
}