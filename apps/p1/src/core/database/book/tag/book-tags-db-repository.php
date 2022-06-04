<?php

namespace p1\core\database\book\tag;

require_once "core/database/book/tag/find-all-book-tags-query.php";

require_once "core/domain/book/tag/book-tags-repository.php";

use p1\core\domain\book\tag\BookTagsRepository;

class BookTagsDbRepository implements BookTagsRepository {
  private FindAllBookTagsQuery $findAllBookTagsQuery;

  public function __construct(FindAllBookTagsQuery $findAllBookTagsQuery) {
    $this->findAllBookTagsQuery = $findAllBookTagsQuery;
  }

  function findAll(): array {
    return $this->findAllBookTagsQuery->query();
  }
}