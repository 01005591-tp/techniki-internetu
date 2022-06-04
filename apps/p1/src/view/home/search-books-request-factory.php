<?php

namespace p1\view\home;

require_once "core/function/function.php";
require_once "session/session-manager.php";

use p1\core\function\Supplier;
use p1\session\SessionConstants;
use p1\session\SessionManager;

class SearchBooksRequestFactory
{
    private SessionManager $sessionManager;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function currentRequest(): SearchBooksRequest
    {
        return $this->sessionManager->get(SessionConstants::BOOK_LIST_SEARCH_REQUEST)
            ->orElseGet(new class implements Supplier {
                function supply(): SearchBooksRequest
                {
                    return SearchBooksRequest::defaultRequest();
                }
            });
    }

    public function create(): SearchBooksRequest
    {
        $lastRequest = $this->resolveLatestRequest();
        $newRequest = $this->resolveSearchCriteria($lastRequest);
        $this->setCurrentRequest($newRequest);
        return $newRequest;
    }

    private function resolvePage(): int
    {
        if (array_key_exists('page', $_GET)) {
            $pageQueryParam = htmlspecialchars($_GET['page']);
            if (!is_numeric($pageQueryParam) || $pageQueryParam < 1) {
                $pageQueryParam = 1;
            }
            return $pageQueryParam;
        } else {
            return 1;
        }
    }

    private function resolveSearchCriteria(SearchBooksRequest $latestRequest): SearchBooksRequest
    {
        $page = $this->resolvePage();
        if (isset($_POST['book-search-search-btn'])) {
            return new SearchBooksRequest(
                $latestRequest->page(),
                $latestRequest->pageSize(),
                $_POST['searchBookTitleInput'],
                $_POST['searchBookDescriptionInput'],
                $this->parseSelectedTags($_POST['bookSearchTags_valueHolderInput']),
                $_POST['searchBookAuthorInput'],
                $_POST['searchBookIsbnInput']
            );
        } else if (array_key_exists('searchBookTitleInput', $_GET)) {
            return new SearchBooksRequest(
                $latestRequest->page(),
                $latestRequest->pageSize(),
                $_GET['searchBookTitleInput'],
                $_GET['searchBookDescriptionInput'],
                $this->parseSelectedTags($_GET['bookSearchTags_valueHolderInput']),
                $_GET['searchBookAuthorInput'],
                $_GET['searchBookIsbnInput']
            );
        } else if ($latestRequest->page() !== $page) {
            return $latestRequest->withPage($page);
        } else {
            return $latestRequest;
        }
    }

    private function resolveLatestRequest(): SearchBooksRequest
    {
        $lastRequest = $this->currentRequest();
        $requestUri = $this->sessionManager->get(SessionConstants::REQUEST_URI)->orElse('');
        $requestType = $this->resolveRequestType($requestUri);
        return $requestType === RequestType::DEFAULT
            ? $lastRequest->withCriteriaCleared()
            : $lastRequest;
    }

    private function setCurrentRequest(SearchBooksRequest $request): void
    {
        $this->sessionManager->put(SessionConstants::BOOK_LIST_SEARCH_REQUEST, $request);
    }

    private function resolveRequestType(string $requestUri): RequestType
    {
        return str_starts_with($requestUri, '/book-list')
            ? RequestType::SEARCH
            : RequestType::DEFAULT;
    }

    private function parseSelectedTags(?string $tags): array
    {
        if (empty($tags)) {
            return [];
        }
        return explode(',', $tags);
    }
}

enum RequestType
{
    case SEARCH;
    case DEFAULT;
}