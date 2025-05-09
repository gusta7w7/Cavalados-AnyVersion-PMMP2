<?php

/*
 *
 *    ____ _                   _
 *  / ___| | _____      _____| |_ ___  _ __   ___
 * | |  _| |/ _ \ \ /\ / / __| __/ _ \| '_ \ / _ \
 * | |_| | | (_) \ V  V /\__ \ || (_) | | | |  __/
 *  \____|_|\___/ \_/\_/ |___/\__\___/|_| |_|\___|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Glowstone (Lemdy)
 * @link vk.com/weany
 *
 */

namespace pocketmine\command;

use pocketmine\Thread;
use pocketmine\utils\MainLogger;
use pocketmine\utils\Utils;

use function extension_loaded;
use function feof;
use function fgets;
use function fopen;
use function function_exists;
use function getopt;
use function is_resource;
use function posix_isatty;
use function readline_add_history;
use function readline_callback_handler_install;
use function readline_callback_handler_remove;
use function readline_callback_read_char;
use function stream_select;
use function trim;

class CommandReader extends Thread
{
    private $readline;
    /** @var \Threaded */
    protected $buffer;
    private $shutdown = false;
    private $stdin;
    /** @var MainLogger */
    private $logger;

    public function __construct($logger)
    {
        $this->stdin = fopen("php://stdin", "r");
        $opts = getopt("", ["disable-readline"]);
        if (extension_loaded("readline") && !isset($opts["disable-readline"]) && (!function_exists("posix_isatty") || posix_isatty($this->stdin))) {
            $this->readline = true;
        } else {
            $this->readline = false;
        }
        $this->logger = $logger;
        $this->buffer = new \Threaded();
        $this->start();
    }

    public function shutdown()
    {
        $this->shutdown = true;
    }

    private function readline_callback($line)
    {
        if ($line !== "") {
            $this->buffer[] = $line;
            readline_add_history($line);
        }
    }

    private function readLine()
    {
        if (!$this->readline) {
            $line = trim(fgets($this->stdin));
            if ($line !== "") {
                $this->buffer[] = $line;
            }
        } else {
            readline_callback_read_char();
        }
    }

    /**
     * Reads a line from console, if available. Returns null if not available
     *
     * @return string|null
     */
    public function getLine()
    {
        if ($this->buffer->count() !== 0) {
            return $this->buffer->shift();
        }

        return null;
    }

    public function quit()
    {
        $this->shutdown();
        // Windows sucks
        if (Utils::getOS() != "win") {
            parent::quit();
        }
    }

    public function run()
    {
        if ($this->readline) {
            readline_callback_handler_install("Genisys> ", [$this, "readline_callback"]);
            $this->logger->setConsoleCallback("readline_redisplay");
        }

        while (!$this->shutdown) {
            $r = [$this->stdin];
            $w = null;
            $e = null;
            if (stream_select($r, $w, $e, 0, 200000) > 0) {
                // PHP on Windows sucks
                if (feof($this->stdin)) {
                    if (Utils::getOS() == "win") {
                        $this->stdin = fopen("php://stdin", "r");
                        if (!is_resource($this->stdin)) {
                            break;
                        }
                    } else {
                        break;
                    }
                }
                $this->readLine();
            }
        }

        if ($this->readline) {
            $this->logger->setConsoleCallback(null);
            readline_callback_handler_remove();
        }
    }

    public function getThreadName()
    {
        return "Console";
    }
}
