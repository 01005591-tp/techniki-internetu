<?php

namespace p1\core\observable;

require_once "observable.php";

class ObservableMap {
  protected EntryPutObservables $entryPutObservables;
  protected EntryRemovedObservables $entryRemovedObservables;

  private array $map;

  public function __construct() {
    $this->entryPutObservables = new EntryPutObservables();
    $this->entryRemovedObservables = new EntryRemovedObservables();
    $this->map = array();
  }

  public function get(string $key) {
    if (array_key_exists($key, $this->map)) {
      return $this->map[$key];
    } else {
      return null;
    }
  }

  public function put(string $key, mixed $value): ObservableMap {
    $this->map[$key] = $value;
    $this->entryPutObservables->next($key, $value);
    return $this;
  }

  public function remove(string $key): ObservableMap {
    if (array_key_exists($key, $this->map)) {
      $value = $this->map[$key];
      unset($this->map[$key]);
      $this->entryRemovedObservables->next($key, $value);
    }
    return $this;
  }

  public function clear(): ObservableMap {
    foreach ($this->map as $key => $value) {
      unset($this->map[$key]);
      $this->entryRemovedObservables->next($key, $value);
    }
    return $this;
  }

  public function subscribe(): ObservableMapSubscribeBuilder {
    $subscribeConsumer = new class implements ObservableMapSubscribeConsumer {
      public function consume(SimpleTypedObservable $observables, TypedSubscriber $subscriber): void {
        $observables->subscribe($subscriber);
      }
    };
    return new ObservableMapSubscribeBuilder($this, $subscribeConsumer);
  }

  public function unsubscribe(): ObservableMapSubscribeBuilder {
    $subscribeConsumer = new class implements ObservableMapSubscribeConsumer {
      public function consume(SimpleTypedObservable $observables, TypedSubscriber $subscriber): void {
        $observables->unsubscribe($subscriber);
      }
    };
    return new ObservableMapSubscribeBuilder($this, $subscribeConsumer);
  }
}

class ObservableMapSubscribeBuilder extends ObservableMap {
  private ObservableMap $delegate;
  private ObservableMapSubscribeConsumer $consumer;

  public function __construct(ObservableMap $delegate, ObservableMapSubscribeConsumer $consumer) {
    parent::__construct();
    $this->delegate = $delegate;
    $this->consumer = $consumer;
  }

  public function entryPut(EntryPutSubscriber $subscriber): ObservableMap {
    $this->consumer->consume($this->delegate->entryPutObservables, $subscriber);
    return $this->delegate;
  }

  public function entryRemoved(EntryRemovedSubscriber $subscriber): ObservableMap {
    $this->consumer->consume($this->delegate->entryRemovedObservables, $subscriber);
    return $this->delegate;
  }
}

class EntryPutObservables extends SimpleTypedObservable {
}

class EntryRemovedObservables extends SimpleTypedObservable {
}

interface EntryPutSubscriber extends TypedSubscriber {
}

interface EntryRemovedSubscriber extends TypedSubscriber {
}

interface ObservableMapSubscribeConsumer {
  function consume(SimpleTypedObservable $observables, TypedSubscriber $subscriber): void;
}