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

use pocketmine\event\TextContainer;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\MainLogger;

use function explode;
use function trim;

class ConsoleCommandSender implements CommandSender
{
    private $perm;

    public function __construct()
    {
        $this->perm = new PermissibleBase($this);
    }

    /**
     * @param \pocketmine\permission\Permission|string $name
     *
     * @return bool
     */
    public function isPermissionSet($name)
    {
        return $this->perm->isPermissionSet($name);
    }

    /**
     * @param \pocketmine\permission\Permission|string $name
     *
     * @return bool
     */
    public function hasPermission($name)
    {
        return $this->perm->hasPermission($name);
    }

    /**
     * @param Plugin $plugin
     * @param string $name
     * @param bool   $value
     *
     * @return \pocketmine\permission\PermissionAttachment
     */
    public function addAttachment(Plugin $plugin, $name = null, $value = null)
    {
        return $this->perm->addAttachment($plugin, $name, $value);
    }

    /**
     * @param PermissionAttachment $attachment
     *
     * @return void
     */
    public function removeAttachment(PermissionAttachment $attachment)
    {
        $this->perm->removeAttachment($attachment);
    }

    public function recalculatePermissions()
    {
        $this->perm->recalculatePermissions();
    }

    /**
     * @return \pocketmine\permission\PermissionAttachmentInfo[]
     */
    public function getEffectivePermissions()
    {
        return $this->perm->getEffectivePermissions();
    }

    /**
     * @return bool
     */
    public function isPlayer()
    {
        return false;
    }

    /**
     * @return \pocketmine\Server
     */
    public function getServer()
    {
        return Server::getInstance();
    }

    /**
     * @param string $message
     */
    public function sendMessage($message)
    {
        if ($message instanceof TextContainer) {
            $message = $this->getServer()->getLanguage()->translate($message);
        } else {
            $message = $this->getServer()->getLanguage()->translateString($message);
        }

        foreach (explode("\n", trim($message)) as $line) {
            MainLogger::getLogger()->info($line);
        }
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return "Console";
    }

    /**
     * @return bool
     */
    public function isOp()
    {
        return true;
    }

    /**
     * @param bool $value
     */
    public function setOp($value)
    {

    }
}
