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
 * Implementation of the UT3 Query Protocol (GameSpot)
 * Source: http://wiki.unrealadmin.org/UT3_query_protocol
 */

namespace pocketmine\network\query;

use pocketmine\Server;
use pocketmine\utils\Binary;

use function chr;
use function hash;
use function microtime;
use function ord;
use function random_bytes;
use function strlen;
use function substr;

class QueryHandler
{
    private $server;
    private $lastToken;
    private $token;
    private $longData;
    private $shortData;
    private $timeout;

    const HANDSHAKE = 9;
    const STATISTICS = 0;

    public function __construct()
    {
        $this->server = Server::getInstance();
        $this->server->getLogger()->info($this->server->getLanguage()->translateString("pocketmine.server.query.start"));
        $addr = ($ip = $this->server->getIp()) != "" ? $ip : "0.0.0.0";
        $port = $this->server->getPort();
        $this->server->getLogger()->info($this->server->getLanguage()->translateString("pocketmine.server.query.info", [$port]));
        /*
        The Query protocol is built on top of the existing Minecraft PE UDP network stack.
        Because the 0xFE packet does not exist in the MCPE protocol,
        we can identify	Query packets and remove them from the packet queue.

        Then, the Query class handles itself sending the packets in raw form, because
        packets can conflict with the MCPE ones.
        */

        $this->regenerateToken();
        $this->lastToken = $this->token;
        $this->regenerateInfo();
        $this->server->getLogger()->info($this->server->getLanguage()->translateString("pocketmine.server.query.running", [$addr, $port]));
    }

    public function regenerateInfo()
    {
        $ev = $this->server->getQueryInformation();
        $this->longData = $ev->getLongQuery();
        $this->shortData = $ev->getShortQuery();
        $this->timeout = microtime(true) + $ev->getTimeout();
    }

    public function regenerateToken()
    {
        $this->lastToken = $this->token;
        $this->token = random_bytes(16);
    }

    public static function getTokenString($token, $salt)
    {
        return Binary::readInt(substr(hash("sha512", $salt . ":" . $token, true), 7, 4));
    }

    public function handle($address, $port, $packet)
    {
        $offset = 2;
        $packetType = ord($packet[$offset++]);
        $sessionID = Binary::readInt(substr($packet, $offset, 4));
        $offset += 4;
        $payload = substr($packet, $offset);

        switch ($packetType) {
            case self::HANDSHAKE: //Handshake
                $reply = chr(self::HANDSHAKE);
                $reply .= Binary::writeInt($sessionID);
                $reply .= self::getTokenString($this->token, $address) . "\x00";

                $this->server->getNetwork()->sendPacket($address, $port, $reply);
                break;
            case self::STATISTICS: //Stat
                $token = Binary::readInt(substr($payload, 0, 4));
                if ($token !== self::getTokenString($this->token, $address) && $token !== self::getTokenString($this->lastToken, $address)) {
                    break;
                }
                $reply = chr(self::STATISTICS);
                $reply .= Binary::writeInt($sessionID);

                if ($this->timeout < microtime(true)) {
                    $this->regenerateInfo();
                }

                if (strlen($payload) === 8) {
                    $reply .= $this->longData;
                } else {
                    $reply .= $this->shortData;
                }
                $this->server->getNetwork()->sendPacket($address, $port, $reply);
                break;
        }
    }
}
