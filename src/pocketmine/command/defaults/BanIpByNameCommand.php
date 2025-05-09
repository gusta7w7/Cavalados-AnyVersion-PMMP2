<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;

use function array_shift;
use function count;
use function implode;

class BanIpByNameCommand extends VanillaCommand
{
    public function __construct($name)
    {
        parent::__construct(
            $name,
            "%pocketmine.command.banipbyname.description",
            "%commands.banipbyname.usage"
        );
        $this->setPermission("pocketmine.command.banipbyname");
    }

    public function execute(CommandSender $sender, $currentAlias, array $args)
    {
        if (!$this->testPermission($sender)) {
            return true;
        }

        if (count($args) === 0) {
            $sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

            return false;
        }

        $name = array_shift($args);
        $reason = implode(" ", $args);

        if ($sender->getServer()->getPlayer($name) instanceof Player) {
            $target = $sender->getServer()->getPlayer($name);
        } else {
            return false;
        }

        $sender->getServer()->getIPBans()->addBan($target->getAddress(), $reason, null, $sender->getName());

        if (($player = $sender->getServer()->getPlayerExact($name)) instanceof Player) {
            $player->kick($reason !== "" ? "Banned by admin. Reason:" . $reason : "Banned by admin.");
        }

        Command::broadcastCommandMessage($sender, new TranslationContainer("%commands.banipbyname.success", [$player !== null ? $player->getName() : $name]));

        return true;
    }
}
