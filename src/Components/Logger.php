<?php

namespace crablor\Components;

/**
 * Simple logger.
 */
class Logger
{
    const LEVEL_QUIET = 3;
    const LEVEL_NORMAL = 5;
    const LEVEL_VERBOSE = 7;

    /**
     * @var mixed
     */
    protected $stdout;

    /**
     * @var mixed
     */
    protected $stderr;

    /**
     * @var int
     */
    protected $level;

    /**
     * Logger constructor.
     *
     * @param int $level
     * @param mixed $stdout
     * @param mixed $stderr
     */
    public function __construct(int $level, $stdout = null, $stderr = null)
    {
        if (!$stdout) {
            $this->stdout = fopen('php://stdout', 'w');
        } else {
            $this->stdout = $stdout;
        }

        if (!$stderr) {
            $this->stderr = fopen('php://stderr', 'w');
        } else {
            $this->stderr = $stderr;
        }

        $this->level = $level;
    }

    /**
     * Prints message directly to stdout.
     *
     * @param string $message
     * @param bool $newline
     */
    public function message(string $message, bool $newline = true): void
    {
        $this->toStdout($message, $newline);
    }

    /**
     * Prints info message.
     *
     * @param string $message
     * @param bool $newline
     */
    public function info(string $message, bool $newline = true): void
    {
        if ($this->level < self::LEVEL_NORMAL) {
            return;
        }

        $this->toStderr(sprintf('[INFO] %s', $message), $newline);
    }

    /**
     * Continues info message.
     *
     * @param string $message
     */
    public function continueInfo(string $message): void
    {
        if ($this->level < self::LEVEL_NORMAL) {
            return;
        }

        $this->toStderr($message);
    }

    /**
     * Prints debug message.
     *
     * @param string $message
     * @param bool $newline
     */
    public function debug(string $message, bool $newline = true): void
    {
        if ($this->level < self::LEVEL_VERBOSE) {
            return;
        }

        $this->toStderr(sprintf('[DEBUG] %s', $message), $newline);
    }

    /**
     * Prints error message.
     *
     * @param string $message
     * @param bool $newline
     */
    public function error(string $message, bool $newline = true): void
    {
        $this->toStderr(sprintf('[ERROR] %s', $message), $newline);
    }

    /**
     * Directly prints to $this->stdout.
     *
     * @param string $message
     * @param bool $newline
     */
    protected function toStdout(string $message, bool $newline = true): void
    {
        if ($newline) {
            $message .= PHP_EOL;
        }

        fwrite(
            $this->stdout,
            $message
        );
    }

    /**
     * Directly prints to $this->stderr.
     *
     * @param string $message
     * @param bool $newline
     */
    protected function toStderr(string $message, bool $newline = true): void
    {
        if ($newline) {
            $message .= PHP_EOL;
        }

        fwrite(
            $this->stderr,
            $message
        );
    }
}
