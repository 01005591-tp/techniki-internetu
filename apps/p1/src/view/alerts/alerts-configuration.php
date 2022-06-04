<?php

namespace p1\view\alerts;

use p1\core\observable\Subscriber;
use p1\session\SessionManager;

require_once "session/session-manager.php";
require_once "view/alerts/alert-service.php";

class AlertsConfiguration {
  private AlertsStore $alertsStore;
  private AlertService $alertService;
  private AlertPrinter $alertPrinter;

  public function __construct(SessionManager $sessionManager) {
    $this->alertsStore = new AlertsStore($sessionManager);
    new AlertsChangedListener($this->alertsStore);
    $this->alertService = new AlertService($this->alertsStore);
    $this->alertPrinter = new AlertPrinter($this->alertsStore);
  }

  public function alertService(): AlertService {
    return $this->alertService;
  }

  public function alertPrinter(): AlertPrinter {
    return $this->alertPrinter;
  }
}

class AlertsChangedListener {
  public function __construct(AlertsStore $alertsStore) {
    $subscriber = new class implements Subscriber {
      function onNext($item) {
        require "view/alerts/alerts.php";
      }
    };
    $alertsStore->subscribe($subscriber);
  }
}