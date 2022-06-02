<?php

namespace p1\view\book;

require_once "core/function/either.php";
require_once "core/function/function.php";
require_once "core/function/option.php";

require_once "core/domain/book/book-details.php";
require_once "core/domain/book/get-book-details-command-handler.php";

require_once "session/session-manager.php";

require_once "view/alerts/alert-service.php";
require_once "view/redirect-manager.php";

use L;
use p1\core\domain\book\BookDetails;
use p1\core\domain\book\GetBookDetailsCommand;
use p1\core\domain\book\GetBookDetailsCommandHandler;
use p1\core\domain\Failure;
use p1\core\function\Either;
use p1\core\function\Function2;
use p1\core\function\FunctionIdentity;
use p1\core\function\Supplier;
use p1\view\RedirectManager;
use p1\view\session\SessionConstants;
use p1\view\session\SessionManager;

class BookController
{
    private SessionManager $sessionManager;
    private RedirectManager $redirectManager;
    private GetBookDetailsFunction $getBookDetailsFunction;
    private GlobalErrorMessageEitherSupplier $globalErrorMessageEitherSupplier;
    private WrapIntoEitherRightFunction $wrapIntoEitherRightFunction;

    public function __construct(GetBookDetailsCommandHandler $getBookDetailsCommandHandler,
                                SessionManager               $sessionManager,
                                RedirectManager              $redirectManager)
    {
        $this->sessionManager = $sessionManager;
        $this->redirectManager = $redirectManager;
        $this->globalErrorMessageEitherSupplier = new GlobalErrorMessageEitherSupplier();
        $this->wrapIntoEitherRightFunction = new WrapIntoEitherRightFunction();
        $this->getBookDetailsFunction = new GetBookDetailsFunction(
            $getBookDetailsCommandHandler,
            $this->globalErrorMessageEitherSupplier,
            $this->wrapIntoEitherRightFunction
        );
    }

    public function getBookDetails(): BookDetails
    {
        return $this->resolveBookNameIdPathParam()
            ->flatMapRight($this->getBookDetailsFunction)
            ->fold(
                new RedirectTo404PageNotFoundFunction($this->redirectManager),
                FunctionIdentity::instance()
            );
    }

    private function resolveBookNameIdPathParam(): Either
    {
        return $this->sessionManager->get(SessionConstants::BOOK_DETAILS_BOOK_NAME_ID)
            ->fold(
                $this->globalErrorMessageEitherSupplier,
                $this->wrapIntoEitherRightFunction
            );
    }
}

class GlobalErrorMessageEitherSupplier implements Supplier
{
    function supply(): Either
    {
        return Either::ofLeft(Failure::of(L::main_errors_global_global_error_message));
    }
}

class WrapIntoEitherRightFunction implements Function2
{
    function apply($value): Either
    {
        return Either::ofRight($value);
    }
}

class GetBookDetailsFunction implements Function2
{
    private GetBookDetailsCommandHandler $getBookDetailsCommandHandler;
    private GlobalErrorMessageEitherSupplier $globalErrorMessageEitherSupplier;
    private WrapIntoEitherRightFunction $wrapIntoEitherRightFunction;

    public function __construct(GetBookDetailsCommandHandler     $getBookDetailsCommandHandler,
                                GlobalErrorMessageEitherSupplier $globalErrorMessageEitherSupplier,
                                WrapIntoEitherRightFunction      $wrapIntoEitherRightFunction)
    {
        $this->getBookDetailsCommandHandler = $getBookDetailsCommandHandler;
        $this->globalErrorMessageEitherSupplier = $globalErrorMessageEitherSupplier;
        $this->wrapIntoEitherRightFunction = $wrapIntoEitherRightFunction;
    }

    function apply($value): Either
    {
        $bookNameId = $value;
        return $this->getBookDetailsCommandHandler->handle(new GetBookDetailsCommand($bookNameId))
            ->fold($this->globalErrorMessageEitherSupplier, $this->wrapIntoEitherRightFunction);
    }
}

class RedirectTo404PageNotFoundFunction implements Function2
{
    private RedirectManager $redirectManager;

    public function __construct(RedirectManager $redirectManager)
    {
        $this->redirectManager = $redirectManager;
    }

    function apply($value)
    {
        $this->redirectManager->redirectTo404NotFoundPage()->run();
        return null;
    }
}