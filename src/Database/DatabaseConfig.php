<?php

declare(strict_types=1);

namespace Zorachka\Framework\Database;

use Webmozart\Assert\Assert;

final class DatabaseConfig
{
    private string $host;
    private string $name;
    private string $username;
    private string $password;

    public function __construct(
        string $host,
        string $name,
        string $username,
        string $password,
    ) {
        Assert::notEmpty($host);
        Assert::notEmpty($name);
        Assert::notEmpty($username);
        $this->host = $host;
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
    }

    public static function withDefaults(
        string $host = 'localhost',
        string $name = 'app',
        string $username = 'root',
        string $password = '',
    ) {
        return new self(
            $host, $name, $username, $password,
        );
    }

    /**
     * @return string
     */
    public function host(): string
    {
        return $this->host;
    }

    public function withHost(string $host): self
    {
        Assert::notEmpty($host);
        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function withName(string $name): self
    {
        Assert::notEmpty($name);
        $new = clone $this;
        $new->name = $name;

        return $new;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    public function withUsername(string $username): self
    {
        Assert::notEmpty($username);
        $new = clone $this;
        $new->username = $username;

        return $new;
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->password;
    }

    public function withPassword(string $password): self
    {
        Assert::notEmpty($password);
        $new = clone $this;
        $new->password = $password;

        return $new;
    }
}
