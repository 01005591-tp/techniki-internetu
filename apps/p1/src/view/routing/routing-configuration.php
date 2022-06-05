<?php

namespace p1\view\routing;

require_once "session/session-manager.php";

require_once "view/routing/edit-book-endpoint-resolver.php";
require_once "view/routing/get-book-details-endpoint-resolver.php";

use p1\session\SessionManager;

class RoutingConfiguration {
  private EditBookEndpointResolver $editBookEndpointResolver;
  private GetBookDetailsEndpointResolver $getBookDetailsEndpointResolver;

  public function __construct(SessionManager $sessionManager) {
    $this->getBookDetailsEndpointResolver = new GetBookDetailsEndpointResolver($sessionManager);
    $this->editBookEndpointResolver = new EditBookEndpointResolver($sessionManager);
  }

  public function endpointResolvers(): array {
    return [
      $this->editBookEndpointResolver,
      $this->getBookDetailsEndpointResolver
    ];
  }
}