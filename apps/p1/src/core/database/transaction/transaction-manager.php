<?php

namespace p1\core\database\transaction;

interface TransactionManager {
  function begin();

  function commit();

  function rollback();
}