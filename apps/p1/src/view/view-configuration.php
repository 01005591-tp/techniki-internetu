<?php

namespace p1\view;

require_once "core/domain/book/get-book-list-command-handler.php";
require_once "core/domain/book/get-book-details-command-handler.php";
require_once "core/domain/book/edit/save-book-command-handler.php";
require_once "core/domain/book/tag/get-all-book-tags-use-case.php";

require_once "core/domain/user/create-user-command-handler.php";
require_once "core/domain/user/auth/authenticate-user-command-handler.php";

require_once "session/session-manager.php";

require_once "state.php";

require_once "view/redirect-manager.php";

require_once "view/alerts/alerts-configuration.php";

require_once "view/books/book-configuration.php";
require_once "view/books/book-controller.php";
require_once "view/books/edition/book-edit-controller.php";

require_once "view/home/home-configuration.php";
require_once "view/home/home-controller.php";

require_once "view/login/login-configuration.php";
require_once "view/login/login-controller.php";
require_once "view/login/sign-out/sign-out-controller.php";
require_once "view/login/sign-up/sign-up-controller.php";

require_once "view/navbar/navbar-configuration.php";
require_once "view/navbar/navbar-controller.php";

require_once "view/pagination/pagination-service.php";

require_once "view/security/html-sanitizer.php";

use p1\core\domain\book\edit\SaveBookCommandHandler;
use p1\core\domain\book\GetBookDetailsCommandHandler;
use p1\core\domain\book\GetBookListCommandHandler;
use p1\core\domain\book\tag\GetAllBookTagsUseCase;
use p1\core\domain\user\auth\AuthenticateUserCommandHandler;
use p1\core\domain\user\CreateUserCommandHandler;
use p1\session\SessionManager;
use p1\state\State;
use p1\view\alerts\AlertPrinter;
use p1\view\alerts\AlertsConfiguration;
use p1\view\alerts\AlertService;
use p1\view\book\BookConfiguration;
use p1\view\book\BookController;
use p1\view\books\edition\BookEditController;
use p1\view\home\HomeConfiguration;
use p1\view\home\HomeController;
use p1\view\home\PaginationService;
use p1\view\login\LoginConfiguration;
use p1\view\login\LoginController;
use p1\view\login\signout\SignOutController;
use p1\view\login\signup\SignUpController;
use p1\view\navbar\NavbarConfiguration;
use p1\view\navbar\NavbarController;
use p1\view\security\HtmlSanitizer;

class ViewConfiguration {
  private ViewControllers $controllers;
  private AlertsConfiguration $alertsConfiguration;
  private SessionManager $sessionManager;

  public function __construct(State                          $state,
                              CreateUserCommandHandler       $createUserCommandHandler,
                              AuthenticateUserCommandHandler $authenticateUserCommandHandler,
                              GetBookListCommandHandler      $getBookListCommandHandler,
                              GetBookDetailsCommandHandler   $getBookDetailsCommandHandler,
                              GetAllBookTagsUseCase          $getAllBookTagsUseCase,
                              SaveBookCommandHandler         $saveBookCommandHandler,
                              RedirectManager                $redirectManager,
                              SessionManager                 $sessionManager,
                              PaginationService              $paginationService,
                              HtmlSanitizer                  $htmlSanitizer) {
    $this->sessionManager = $sessionManager;
    $this->alertsConfiguration = new AlertsConfiguration($this->sessionManager);

    $loginConfiguration = new LoginConfiguration(
      $state,
      $createUserCommandHandler,
      $authenticateUserCommandHandler,
      $this->sessionManager,
      $redirectManager,
      $this->alertService()
    );
    $navbarConfiguration = new NavbarConfiguration(
      $state,
      $this->sessionManager
    );
    $homeConfiguration = new HomeConfiguration(
      $this->sessionManager,
      $getBookListCommandHandler,
      $getAllBookTagsUseCase,
      $paginationService
    );
    $bookConfiguration = new BookConfiguration(
      $this->sessionManager,
      $redirectManager,
      $getBookDetailsCommandHandler,
      $saveBookCommandHandler,
      $htmlSanitizer,
      $this->alertService()
    );

    $this->controllers = new ViewControllers(
      $loginConfiguration,
      $navbarConfiguration,
      $homeConfiguration,
      $bookConfiguration
    );
  }

  public function controllers(): ViewControllers {
    return $this->controllers;
  }

  public function alertPrinter(): AlertPrinter {
    return $this->alertsConfiguration->alertPrinter();
  }

  public function alertService(): AlertService {
    return $this->alertsConfiguration->alertService();
  }
}

class ViewControllers {
  private LoginConfiguration $loginConfiguration;
  private NavbarConfiguration $navbarConfiguration;
  private HomeConfiguration $homeConfiguration;
  private BookConfiguration $bookConfiguration;

  public function __construct(LoginConfiguration  $loginConfiguration,
                              NavbarConfiguration $navbarConfiguration,
                              HomeConfiguration   $homeConfiguration,
                              BookConfiguration   $bookConfiguration) {
    $this->loginConfiguration = $loginConfiguration;
    $this->navbarConfiguration = $navbarConfiguration;
    $this->homeConfiguration = $homeConfiguration;
    $this->bookConfiguration = $bookConfiguration;
  }

  public function navbarController(): NavbarController {
    return $this->navbarConfiguration->navbarController();
  }

  public function signUpController(): SignUpController {
    return $this->loginConfiguration->signUpController();
  }

  public function loginController(): LoginController {
    return $this->loginConfiguration->loginController();
  }

  public function signOutController(): SignOutController {
    return $this->loginConfiguration->signOutController();
  }

  public function homeController(): HomeController {
    return $this->homeConfiguration->homeController();
  }

  public function bookController(): BookController {
    return $this->bookConfiguration->bookController();
  }

  public function bookEditController(): BookEditController {
    return $this->bookConfiguration->bookEditController();
  }
}