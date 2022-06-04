<?php

namespace p1\core\database\user\auth;

require_once "core/domain/user/auth/user-auth-repository.php";

use p1\core\domain\user\auth\UserAuthRepository;

class UserAuthDbRepository implements UserAuthRepository {
  private FindUserRolesByUserIdQuery $findUserRolesByUserIdQuery;

  public function __construct(FindUserRolesByUserIdQuery $findUserRolesByUserIdQuery) {
    $this->findUserRolesByUserIdQuery = $findUserRolesByUserIdQuery;
  }

  public function findUserRoles(string $id): array {
    return $this->findUserRolesByUserIdQuery->query($id);
  }
}