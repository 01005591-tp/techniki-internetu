<?php

namespace p1\view\routing;

require_once "session/session-manager.php";

require_once "state.php";

require_once "view/books/edition/book-edit-controller.php";

require_once "view/navbar/navbar-controller.php";

require_once "view/routing/endpoint-resolver.php";

use p1\core\domain\user\auth\Roles;
use p1\session\SessionConstants;
use p1\session\SessionManager;
use p1\state\State;
use p1\view\navbar\NavbarItem;

class EditBookEndpointResolver implements EndpointResolver {
  private const EDIT_BOOK_ENDPOINT_REGEX = '/^(\/books\/)([^\/\?]+)(\/edition)$/';
  private const EDIT_BOOK_ENDPOINT_BOOK_NAME_ID_PATH_PARAM_REGEX = '/^(\/books\/)([^\/\?]+)(\/edition)$/';

  private SessionManager $sessionManager;

  public function __construct(SessionManager $sessionManager) {
    $this->sessionManager = $sessionManager;
  }

  function resolve(string $request): bool {
    if (!$this->sessionManager->userContext()->hasAnyRole(Roles::EMPLOYEE->name)) {
      return false;
    }
    if (preg_match(EditBookEndpointResolver::EDIT_BOOK_ENDPOINT_REGEX, $request)) {
      $bookNameId = preg_replace(EditBookEndpointResolver::EDIT_BOOK_ENDPOINT_BOOK_NAME_ID_PATH_PARAM_REGEX, "$2", $request);
      $this->sessionManager->put(SessionConstants::BOOK_DETAILS_BOOK_NAME_ID, $bookNameId);
      State::instance()->put(State::ACTIVE_ITEM_KEY, NavbarItem::BookEdition);
      return true;
    } else {
      return false;
    }
  }
}