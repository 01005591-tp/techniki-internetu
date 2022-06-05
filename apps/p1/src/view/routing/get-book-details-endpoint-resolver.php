<?php

namespace p1\view\routing;

require_once "session/session-manager.php";
require_once "state.php";
require_once "view/navbar/navbar-controller.php";

use p1\session\SessionConstants;
use p1\session\SessionManager;
use p1\state\State;
use p1\view\navbar\NavbarItem;

class GetBookDetailsEndpointResolver implements EndpointResolver {
  private const GET_SINGLE_BOOK_ENDPOINT_REGEX = '/^(\/books\/)([^\/\?]+)$/';
  private const GET_SINGLE_BOOK_ENDPOINT_BOOK_NAME_ID_PATH_PARAM_REGEX = '/^(\/books\/)([^\/\?]+)$/';

  private SessionManager $sessionManager;

  public function __construct(SessionManager $sessionManager) {
    $this->sessionManager = $sessionManager;
  }

  function resolve(string $request): bool {
    if (preg_match(GetBookDetailsEndpointResolver::GET_SINGLE_BOOK_ENDPOINT_REGEX, $request)) {
      $bookNameId = preg_replace(GetBookDetailsEndpointResolver::GET_SINGLE_BOOK_ENDPOINT_BOOK_NAME_ID_PATH_PARAM_REGEX, "$2", $request);
      $this->sessionManager->put(SessionConstants::BOOK_DETAILS_BOOK_NAME_ID, $bookNameId);
      State::instance()->put(State::ACTIVE_ITEM_KEY, NavbarItem::BookDetails);
      return true;
    } else {
      return false;
    }
  }
}