<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\utils;

use function is_int;

/**
 * Manages PocketMine-MP version strings, and compares them
 */
class VersionString
{
    private $major;
    private $build;
    private $minor;
    private $development = false;

    public function __construct($version = \pocketmine\VERSION)
    {
        if (is_int($version)) {
            $this->minor = $version & 0x1F;
            $this->major = ($version >> 5) & 0x0F;
            $this->generation = ($version >> 9) & 0x0F;
        } else {
            $this->generation = 0;
            $this->major = 0;
            $this->minor = 0;
            $this->development = true;
            $this->build = 0;
        }
    }

    public function getNumber()
    {
        return (int) (($this->generation << 9) + ($this->major << 5) + $this->minor);
    }

    /**
     * @deprecated
     */
    public function getStage()
    {
        return "final";
    }

    public function getGeneration()
    {
        return $this->generation;
    }

    public function getMajor()
    {
        return $this->major;
    }

    public function getMinor()
    {
        return $this->minor;
    }

    public function getRelease()
    {
        return $this->generation . "." . $this->major . ($this->minor > 0 ? "." . $this->minor : "");
    }

    public function getBuild()
    {
        return $this->build;
    }

    public function isDev()
    {
        return $this->development === true;
    }

    public function get($build = false)
    {
        return $this->getRelease() . ($this->development === true ? "dev" : "") . (($this->build > 0 && $build === true) ? "-" . $this->build : "");
    }

    public function __toString()
    {
        return $this->get();
    }

    public function compare($target, $diff = false)
    {
        if (($target instanceof VersionString) === false) {
            $target = new VersionString($target);
        }
        $number = $this->getNumber();
        $tNumber = $target->getNumber();
        if ($diff === true) {
            return $tNumber - $number;
        }
        if ($number > $tNumber) {
            return -1; //Target is older
        } elseif ($number < $tNumber) {
            return 1; //Target is newer
        } elseif ($target->getBuild() > $this->getBuild()) {
            return 1;
        } elseif ($target->getBuild() < $this->getBuild()) {
            return -1;
        } else {
            return 0; //Same version
        }
    }
}
