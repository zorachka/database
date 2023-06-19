<?php

declare(strict_types=1);

namespace Zorachka\Database;

use Webmozart\Assert\Assert;

final class DatabaseConfig
{
    private Driver $driver;
    /**
     * @var non-empty-string
     */
    private string $name;
    /**
     * @var non-empty-string
     */
    private string $host;
    /**
     * @var positive-int
     */
    private int $port;
    /**
     * @var non-empty-string|null
     */
    private ?string $username;
    /**
     * @var non-empty-string|null
     */
    private ?string $password;

    /**
     * @param non-empty-string $name
     * @param non-empty-string $host
     * @param positive-int $port
     * @param non-empty-string|null $username
     * @param non-empty-string|null $password
     */
    private function __construct(
        Driver $driver,
        string $name,
        string $host,
        int $port,
        ?string $username,
        ?string $password,
    ) {
        Assert::notEmpty($name);
        Assert::notEmpty($host);
        Assert::notEmpty($port);

        $this->driver = $driver;
        $this->name = $name;
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $host
     * @param positive-int $port
     * @param non-empty-string|null $username
     * @param non-empty-string|null $password
     */
    public static function withDefaults(
        Driver $driver = Driver::PGSQL,
        string $name = 'app',
        string $host = 'localhost',
        int $port = 5432,
        ?string $username = null,
        ?string $password = null,
    ): self {
        return new self(
            $driver,
            $name,
            $host,
            $port,
            $username,
            $password,
        );
    }

    public function driver(): Driver
    {
        return $this->driver;
    }

    public function withDriver(Driver $driver): self
    {
        $new = clone $this;
        $new->driver = $driver;

        return $new;
    }

    /**
     * @return non-empty-string
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
     * @return positive-int
     */
    public function port(): int
    {
        return $this->port;
    }

    /**
     * @param positive-int $port
     * @return $this
     */
    public function withPort(int $port): self
    {
        Assert::notEmpty($port);
        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * @return non-empty-string
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
     * @return non-empty-string|null
     */
    public function username(): ?string
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
     * @return non-empty-string|null
     */
    public function password(): ?string
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
