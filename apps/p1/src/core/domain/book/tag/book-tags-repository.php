<?php

namespace p1\core\domain\book\tag;

interface BookTagsRepository
{
    function findAll(): array;
}