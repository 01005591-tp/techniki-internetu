<?php

namespace p1\view\alerts;

use p1\core\function\Supplier;
use p1\core\observable\SimpleObservable;
use p1\session\SessionConstants;
use p1\session\SessionManager;

require_once "session/session-manager.php";

class AlertService
{
    private AlertsStore $alertsStore;

    public function __construct(AlertsStore $alertsStore)
    {
        $this->alertsStore = $alertsStore;
    }

    public function success(string $message, array $params = array()): void
    {
        $this->print(AlertType::SUCCESS, $message, $params);
    }

    public function info(string $message, array $params = array()): void
    {
        $this->print(AlertType::INFO, $message, $params);
    }

    public function warning(string $message, array $params = array()): void
    {
        $this->print(AlertType::WARNING, $message, $params);
    }

    public function error(string $message, array $params = array()): void
    {
        $this->print(AlertType::ERROR, $message, $params);
    }

    private function print(AlertType $alertType, string $message, array $params = array()): void
    {
        $printableMessage = $message;
        if (count($params) > 0) {
            foreach ($params as $key => $param) {
                $printableMessage = str_replace("{" . $key . "}", $param, $printableMessage);
            }
        }
        $this->alertsStore->addMessage(new AlertMessage(
            $alertType,
            $printableMessage
        ));
    }
}

class AlertsStore extends SimpleObservable
{
    private SessionManager $sessionManager;
    private array $messages;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
        $this->messages = $this->getMessages();
    }

    public function addMessage(AlertMessage $alertMessage): void
    {
        $this->messages[] = $alertMessage;
        $this->updateMessages();
        $this->next($this->messages);
    }

    public function messages(): array
    {
        return $this->messages;
    }

    public function discardMessages(): void
    {
        $this->messages = [];
        $this->updateMessages();
    }

    public function updateMessages(): void
    {
        $this->sessionManager->put(SessionConstants::ALERT_MESSAGES, $this->messages);
    }

    public function init(): void
    {
        $this->next($this->messages);
    }

    private function getMessages(): array
    {
        return $this->sessionManager->get(SessionConstants::ALERT_MESSAGES)
            ->orElseGet(new class implements Supplier {
                function supply(): array
                {
                    return array();
                }
            });
    }
}

class AlertPrinter
{
    private AlertsStore $alertsStore;

    public function __construct(AlertsStore $alertsStore)
    {
        $this->alertsStore = $alertsStore;
    }

    public function displayAlerts(): void
    {
        foreach ($this->alertsStore->messages() as $message) {
            $alertCssClass = $this->resolveCssClass($message->type());
            echo '<div class="alert alert-' . $alertCssClass . '" role=alert>'
                . $message->message()
                . '</div>';
            $this->alertsStore->discardMessages();
        }
    }

    private function resolveCssClass(AlertType $alertType): string
    {
        return match ($alertType) {
            AlertType::SUCCESS => 'success',
            AlertType::INFO => 'info',
            AlertType::WARNING => 'warning',
            AlertType::ERROR => 'danger'
        };
    }
}

enum AlertType
{
    case SUCCESS;
    case INFO;
    case WARNING;
    case ERROR;
}

class AlertMessage
{
    private AlertType $type;
    private string $message;

    public function __construct(AlertType $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public function type(): AlertType
    {
        return $this->type;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function __toString(): string
    {
        return "{type: " . $this->type->name
            . ", message: " . $this->message . "}";
    }
}