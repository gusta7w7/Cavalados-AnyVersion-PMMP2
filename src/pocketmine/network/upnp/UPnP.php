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

/**
 * UPnP port forwarding support. Only for Windows
 */

namespace pocketmine\network\upnp;

use pocketmine\utils\Utils;

use function class_exists;
use function gethostbyname;
use function is_object;
use function trim;

abstract class UPnP
{
    public static function PortForward($port)
    {
        if (Utils::$online === false) {
            return false;
        }
        if (Utils::getOS() != "win" || !class_exists("COM")) {
            return false;
        }
        $port = (int) $port;
        $myLocalIP = gethostbyname(trim(`hostname`));
        try {
            $com = new \COM("HNetCfg.NATUPnP");
            if ($com === false || !is_object($com->StaticPortMappingCollection)) {
                return false;
            }
            $com->StaticPortMappingCollection->Add($port, "UDP", $port, $myLocalIP, true, "PocketMine-MP");
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    public static function RemovePortForward($port)
    {
        if (Utils::$online === false) {
            return false;
        }
        if (Utils::getOS() != "win" || !class_exists("COM")) {
            return false;
        }
        $port = (int) $port;
        try {
            $com = new \COM("HNetCfg.NATUPnP") || false;
            if ($com === false || !is_object($com->StaticPortMappingCollection)) {
                return false;
            }
            $com->StaticPortMappingCollection->Remove($port, "UDP");
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }
}
