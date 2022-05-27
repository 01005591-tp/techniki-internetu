<?php

namespace p1\core\domain\user;

use p1\core\function\Either;

class CreateUserCommandHandler
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function handle(CreateUserCommand $command): Either
    {
        $passwordHash = password_hash($command->password(), PASSWORD_DEFAULT);
        return $this->userRepository->createUser($command->withPassword($passwordHash));
    }
}