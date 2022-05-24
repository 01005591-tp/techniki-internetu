<?php

namespace p1\core\observable;

interface Subscriber
{
    function onNext($item);
}

interface TypedSubscriber extends Subscriber
{
    function type(): string;
}

interface Observable
{
    function subscribe(Subscriber $subscriber): Observable;

    function unsubscribe(Subscriber $subscriber): Observable;

    function next($item): Observable;
}

interface TypedObservable
{
    function subscribe(TypedSubscriber $subscriber): TypedObservable;

    function unsubscribe(TypedSubscriber $subscriber): TypedObservable;

    function next(string $type, $item = null): TypedObservable;
}

abstract class SimpleSubscriber implements Subscriber
{
    function onNext($item)
    {
    }
}

abstract class SimpleObservable implements Observable
{
    private array $subscribers = array();

    function subscribe(Subscriber $subscriber): Observable
    {
        $this->subscribers[] = $subscriber;
        return $this;
    }

    function unsubscribe(Subscriber $subscriber): Observable
    {
        foreach ($this->subscribers as $key => $existingSubscriber) {
            if ($existingSubscriber === $subscriber) {
                unset($this->subscribers[$key]);
            }
        }
        return $this;
    }

    function next($item): Observable
    {
        foreach ($this->subscribers as $subscriber) {
            $subscriber->onNext($item);
        }
        return $this;
    }
}

abstract class SimpleTypedObservable implements TypedObservable
{
    protected const KEY_ALL = "*";
    private array $subscribers = [];

    public function __construct()
    {
        $this->subscribers[self::KEY_ALL] = [];
    }

    function subscribe(TypedSubscriber $subscriber): TypedObservable
    {
        $type = $subscriber->type();
        $this->initTypeSubscribers($type);
        $this->subscribers[$type][] = $subscriber;
        return $this;
    }

    function unsubscribe(TypedSubscriber $subscriber): TypedObservable
    {
        $type = $subscriber->type();
        foreach ($this->subscribers[$type] as $key => $value) {
            if ($value === $subscriber) {
                unset($this->subscribers[$type][$key]);
            }
        }
        return $this;
    }

    function next(string $type, $item = null): TypedObservable
    {
        foreach ($this->getTypeSubscribers($type) as $subscriber) {
            $subscriber->onNext($item);
        }
        return $this;
    }

    private function initTypeSubscribers(string $type = self::KEY_ALL): void
    {
        if (!isset($this->subscribers[$type])) {
            $this->subscribers[$type] = [];
        }
    }

    private function getTypeSubscribers(string $type = self::KEY_ALL): array
    {
        $this->initTypeSubscribers($type);
        $typeSubscribers = $this->subscribers[$type];
        $allTypesSubscribers = $this->subscribers[self::KEY_ALL];
        return array_merge($typeSubscribers, $allTypesSubscribers);
    }
}