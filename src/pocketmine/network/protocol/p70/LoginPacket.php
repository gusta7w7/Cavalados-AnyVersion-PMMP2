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

namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

use function unpack;

use const PHP_INT_SIZE;

#include <rules/DataPacket.h>


class LoginPacket extends DataPacket
{
    const NETWORK_ID = Info::LOGIN_PACKET;

    public $username;
    public $protocol;
    public $protocol1;
    public $protocol2;
    public $clientId;

    public $clientUUID;
    public $serverAddress;
    public $clientSecret;

    public $skinName = null;
    public $skin = null;

    public function decode()
    {
        $this->username = $this->getString();
        $this->protocol = (PHP_INT_SIZE === 8 ? unpack("N", $this->get(4))[1] << 32 >> 32 : unpack("N", $this->get(4))[1]);
        $this->protocol = (PHP_INT_SIZE === 8 ? unpack("N", $this->get(4))[1] << 32 >> 32 : unpack("N", $this->get(4))[1]);

        $this->protocol = 70;
        //if($this->protocol < 70){ //New fields!
        //    $this->setBuffer(null, 0);
        //    return;
        //}

        $this->clientId = Binary::readLong($this->get(8));
        $this->clientUUID = $this->getUUID();
        $this->serverAddress = $this->getString();
        $this->clientSecret = $this->getString();

        $this->skinName = $this->getString();
        $this->skin = $this->getString();
    }

    public function encode()
    {

    }
}
