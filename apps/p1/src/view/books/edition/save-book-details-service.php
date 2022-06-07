<?php

namespace p1\view\books\edition;

require_once "core/domain/book/book-details.php";
require_once "core/domain/book/edit/save-book-command-handler.php";
require_once "core/domain/book/edit/save-book-command.php";
require_once "core/domain/failure.php";

require_once "core/function/either.php";
require_once "core/function/function.php";

require_once "session/session-manager.php";

require_once "view/redirect-manager.php";
require_once "view/alerts/alert-service.php";
require_once "view/books/book-details-service.php";
require_once "view/books/edition/save-book-details-merge-mapper.php";
require_once "view/books/edition/save-book-details-request-to-command-mapper.php";

use L;
use p1\core\domain\book\BookDetails;
use p1\core\domain\book\edit\SaveBookCommandHandler;
use p1\core\domain\book\edit\SaveBookError;
use p1\core\domain\error\OptimisticLockError;
use p1\core\domain\Failure;
use p1\core\function\Either;
use p1\core\function\FunctionIdentity;
use p1\core\function\FunctionUtils;
use p1\session\SessionConstants;
use p1\session\SessionManager;
use p1\view\alerts\AlertService;
use p1\view\books\BookDetailsService;
use p1\view\RedirectManager;

class SaveBookDetailsService {
  private SaveBookCommandHandler $saveBookCommandHandler;
  private SaveBookDetailsRequestToCommandMapper $bookDetailsRequestToCommandMapper;
  private BookDetailsService $bookDetailsService;
  private SessionManager $sessionManager;
  private RedirectManager $redirectManager;
  private AlertService $alertService;
  private SaveBookDetailsMergeMapper $saveBookDetailsMergeMapper;

  public function __construct(SaveBookCommandHandler                $saveBookCommandHandler,
                              SaveBookDetailsRequestToCommandMapper $saveBookDetailsRequestToCommandMapper,
                              SessionManager                        $sessionManager,
                              BookDetailsService                    $bookDetailsService,
                              RedirectManager                       $redirectManager,
                              AlertService                          $alertService,
                              SaveBookDetailsMergeMapper            $saveBookDetailsMergeMapper) {
    $this->saveBookCommandHandler = $saveBookCommandHandler;
    $this->bookDetailsRequestToCommandMapper = $saveBookDetailsRequestToCommandMapper;
    $this->sessionManager = $sessionManager;
    $this->bookDetailsService = $bookDetailsService;
    $this->redirectManager = $redirectManager;
    $this->alertService = $alertService;
    $this->saveBookDetailsMergeMapper = $saveBookDetailsMergeMapper;
  }

  public function saveBook(array $post): ?BookDetails {
    return $this->bookDetailsRequestToCommandMapper->toCommand($post, $this->sessionManager->userContext())
      ->flatMapRight(FunctionUtils::function2OfClosure(fn($command) => $this->saveBookCommandHandler->save($command)))
      ->peekRight(FunctionUtils::consumerOfClosure(
        fn($savedDetails) => $this->alertService->success(L::main_books_book_piece_edit_page_save_success_msg)
      ))
      ->peekRight(FunctionUtils::consumerOfClosure(
        fn($savedDetails) => $this->sessionManager->get(SessionConstants::BOOK_DETAILS_BOOK_NAME_ID)
          ->filter(FunctionUtils::predicateOfClosure(
            fn($oldNameId) => $oldNameId !== $savedDetails->book()->nameId()
          ))
          ->peek(FunctionUtils::consumerOfClosure(
            fn($oldNameId) => $this->redirectManager->redirectToBookEditionPage($savedDetails->book()->nameId())
          ))
      ))
      ->fold(
        FunctionUtils::function2OfClosure(fn($failure) => $this->handleFailure($failure)),
        FunctionIdentity::instance()
      );
  }

  private function handleFailure(Failure $failure): ?BookDetails {
    // if-else pattern match instance type
    if (is_a($failure, 'p1\core\domain\book\edit\SaveBookError')) {
      return $this->handleSaveBookError($failure);
    } else if (is_a($failure, 'p1\view\books\edition\SaveBookDetailsRequestFailure')) {
      return $this->handleSaveBookDetailsRequestFailure($failure);
    } else if (is_a($failure, 'p1\core\domain\error\OptimisticLockError')) {
      return $this->handleOptimisticLockError($failure);
    } else {
      error_log("SaveBookDetailsService.handleFailure() unexpected error: " . $failure->message());
      $this->redirectManager->redirectTo404NotFoundPage()->run();
      return null;
    }
  }

  private function handleSaveBookError(SaveBookError $failure): ?BookDetails {
    if (is_a($failure->cause(), 'p1\core\domain\error\OptimisticLockError')) {
      return $this->handleOptimisticLockError($failure->cause());
    } else {
      error_log("SaveBookDetailsService.handleFailure() SaveBookError: " . $failure->message());
      $this->alertService->error($failure->message());
      return $this->saveBookDetailsMergeMapper->mergeBookDetailsWithCommand($this->getBookDetails(), $failure->command());
    }
  }

  private function handleSaveBookDetailsRequestFailure(SaveBookDetailsRequestFailure $failure): ?BookDetails {
    error_log("SaveBookDetailsService.handleFailure() SaveBookDetailsRequestFailure: " . $failure->message());
    $this->alertService->error($failure->message());
    return $this->saveBookDetailsMergeMapper->mergeBookDetailsWithRequest($this->getBookDetails(), $failure->request());
  }

  private function handleOptimisticLockError(OptimisticLockError $error): ?BookDetails {
    error_log("SaveBookDetailsService.handleOptimisticLockError() OptimisticLockError: " . $error->message());
    $this->alertService->error(L::main_errors_save_book_result_optimistic_lock_msg);
    return $this->getBookDetails();
  }

  private function getBookDetails(): BookDetails {
    return $this->sessionManager->get(SessionConstants::BOOK_DETAILS_BOOK_NAME_ID)
      ->fold(
        Either::leftSupplier(Failure::of(L::main_errors_global_global_error_message)),
        Either::wrapToRightFunction()
      )
      ->mapRight($this->bookDetailsService->getBookDetailsRequiredFunction())
      ->fold(
        FunctionUtils::runnableToFunction2($this->redirectManager->redirectTo404NotFoundPage()),
        FunctionIdentity::instance()
      );
  }
}