<?php

namespace p1\view\home;

require_once "core/domain/book/book-list.php";
require_once "core/domain/book/get-book-list-command-handler.php";

require_once "core/domain/book/tag/get-all-book-tags-use-case.php";

require_once "core/function/function.php";
require_once "core/function/option.php";

require_once "session/session-manager.php";

require_once "view/pagination/pagination.php";
require_once "view/pagination/pagination-service.php";

require_once "view/home/search-books-request.php";

use p1\core\domain\book\BookList;
use p1\core\domain\book\GetBookListCommand;
use p1\core\domain\book\GetBookListCommandHandler;
use p1\core\domain\book\tag\GetAllBookTagsUseCase;
use p1\core\function\Function2;
use p1\core\function\FunctionIdentity;
use p1\core\function\Option;
use p1\session\SessionManager;

class HomeController {
  private GetBookListCommandHandler $getBookListCommandHandler;
  private GetAllBookTagsUseCase $getAllBookTagsUseCase;
  private SessionManager $sessionManager;
  private PaginationService $paginationService;
  private SearchBooksRequestFactory $searchBooksRequestFactory;
  private BookList $bookList;
  private Option $paginationData;
  private array $availableTags;

  public function __construct(GetBookListCommandHandler $getBookListCommandHandler,
                              GetAllBookTagsUseCase     $getAllBookTagsUseCase,
                              SessionManager            $sessionManager,
                              PaginationService         $paginationService,
                              SearchBooksRequestFactory $searchBooksRequestFactory) {
    $this->getBookListCommandHandler = $getBookListCommandHandler;
    $this->getAllBookTagsUseCase = $getAllBookTagsUseCase;
    $this->sessionManager = $sessionManager;
    $this->paginationService = $paginationService;
    $this->searchBooksRequestFactory = $searchBooksRequestFactory;

    $this->bookList = BookList::emptyBookList();
    $this->paginationData = Option::none();
    $this->availableTags = [];
  }

  public function findBooks(): BookList {
    $request = $this->resolveSearchBooksRequest();
    $this->availableTags = $this->getAllBookTagsUseCase->execute();
    return $this->getBookListPage($request);
  }

  private function getBookListPage(SearchBooksRequest $request): BookList {
    $command = new GetBookListCommand(
      $request->page(),
      $request->pageSize(),
      $request->title(),
      $request->description(),
      $request->tags(),
      $request->author(),
      $request->isbn()
    );
    $this->bookList = $this->getBookListCommandHandler->handle($command)
      ->fold(
        new GetDefaultBookListFailedFunction($this->bookList),
        FunctionIdentity::instance()
      );
    $this->paginationData = $this->resolvePaginationData($request);
    return $this->bookList;
  }

  public function paginationData(): Option {
    return $this->paginationData;
  }

  public function availableTags(): array {
    return $this->availableTags;
  }

  public function searchCriteria(): SearchBooksRequest {
    return $this->searchBooksRequestFactory->currentRequest();
  }

  private function resolvePaginationData(SearchBooksRequest $currentPageData): Option {
    $params = new ResolvePaginationParams(
      '/book-list',
      'page',
      $currentPageData->page(),
      $this->bookList->booksCount(),
      $currentPageData->pageSize()
    );
    return $this->paginationService->resolvePagination($params);
  }


  private function resolveSearchBooksRequest(): SearchBooksRequest {
    return $this->searchBooksRequestFactory->create();

  }
}

class GetDefaultBookListFailedFunction implements Function2 {
  private BookList $currentBookList;

  public function __construct(BookList $currentBookList) {
    $this->currentBookList = $currentBookList;
  }

  function apply($value): BookList {
    error_log('Default book list not found: ' . $value->message());
    return $this->currentBookList;
  }
}
