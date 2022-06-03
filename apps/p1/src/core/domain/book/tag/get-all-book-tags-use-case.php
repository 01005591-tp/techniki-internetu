<?php

namespace p1\core\domain\book\tag;
require_once "core/domain/book/tag/book-tags-repository.php";

class GetAllBookTagsUseCase
{
    private BookTagsRepository $bookTagsRepository;

    public function __construct(BookTagsRepository $bookTagsRepository)
    {
        $this->bookTagsRepository = $bookTagsRepository;
    }

    public function execute(): array
    {
        return $this->bookTagsRepository->findAll();
    }
}