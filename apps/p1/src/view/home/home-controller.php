<?php

namespace p1\view\home;

require_once "core/domain/book/book-list.php";
require_once "core/domain/book/get-book-list-command-handler.php";
require_once "core/function/function.php";
require_once "session/session-manager.php";

use p1\core\domain\book\BookList;
use p1\core\domain\book\BookListPage;
use p1\core\domain\book\GetBookListCommand;
use p1\core\domain\book\GetBookListCommandHandler;
use p1\core\function\Function2;
use p1\core\function\FunctionIdentity;
use p1\core\function\Supplier;
use p1\view\session\SessionConstants;
use p1\view\session\SessionManager;

class HomeController
{
    private GetBookListCommandHandler $getBookListCommandHandler;
    private SessionManager $sessionManager;
    private BookList $bookList;

    public function __construct(GetBookListCommandHandler $getBookListCommandHandler,
                                SessionManager            $sessionManager)
    {
        $this->getBookListCommandHandler = $getBookListCommandHandler;
        $this->sessionManager = $sessionManager;
        $this->bookList = BookList::emptyBookList();
    }

    public function getDefaultBookList(): BookList
    {
        $bookListPage = $this->currentPage();
        $command = new GetBookListCommand($bookListPage->page(), $bookListPage->pageSize());
        $this->bookList = $this->getBookListCommandHandler->handle($command)
            ->fold(
                new GetDefaultBookListFailedFunction($this->bookList),
                FunctionIdentity::instance()
            );
        return $this->bookList;
    }

    public function currentPage(): BookListPage
    {
        return $this->sessionManager->get(SessionConstants::CURRENT_BOOK_LIST_PAGE)
            ->orElseGet(new class implements Supplier {
                function supply(): BookListPage
                {
                    return BookListPage::defaultBookListPage();
                }
            });
    }
}

class GetDefaultBookListFailedFunction implements Function2
{
    private BookList $currentBookList;

    public function __construct(BookList $currentBookList)
    {
        $this->currentBookList = $currentBookList;
    }

    function apply($value): BookList
    {
        error_log('Default book list not found: ' . $value->message());
        return $this->currentBookList;
    }
}