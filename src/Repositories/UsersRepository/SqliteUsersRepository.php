<?php

namespace alxgeras\php2\Repositories\UsersRepository;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Person\Name;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private \PDO            $pdo,
        private LoggerInterface $logger
    )
    {
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidUuidException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE uuid = :uuid');
        $statement->execute([':uuid' => (string)$uuid]);

        return $this->getUser($statement, $uuid);
    }

    public function save(User $user): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users 
              (uuid, username, first_Name, last_Name)
            VALUES 
              (:uuid, :username, :firstName, :lastName)'
        );
        $statement->execute([
            ':uuid' => (string)$user->getUuid(),
            ':username' => $user->getUsername(),
            ':firstName' => $user->getName()->getFirstName(),
            ':lastName' => $user->getName()->getLastName()
        ]);

        $this->logger->info("User created: {$user->getUuid()}");
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidUuidException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $statement->execute([':username' => $username,]);

        return $this->getUser($statement, $username);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidUuidException
     */
    private function getUser(\PDOStatement $statement, string $usernameOrUUID): User
    {
        $result = $statement->fetch();

        if (!$result) {
            $message = 'No user: ' . $usernameOrUUID;

            $this->logger->warning($message);
            throw new UserNotFoundException($message);
        }
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username']
        );
    }

    public function remove(UUID $uuid): void
    {
        $statement = $this->pdo->prepare(
            'DELETE
            FROM users 
            WHERE uuid = :uuid'
        );

        $statement->execute([':uuid' => (string)$uuid]);
        $this->logger->info("User deleted: $uuid");
    }
}