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

namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\math\Vector3;
use pocketmine\Player;

use function count;
use function round;

class TeleportCommand extends VanillaCommand
{
    public function __construct($name)
    {
        parent::__construct(
            $name,
            "%pocketmine.command.tp.description",
            "%commands.tp.usage"
        );
        $this->setPermission("pocketmine.command.teleport");
    }

    public function execute(CommandSender $sender, $currentAlias, array $args)
    {
        if (!$this->testPermission($sender)) {
            return true;
        }

        if (count($args) < 1 || count($args) > 6) {
            $sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

            return true;
        }

        $target = null;
        $origin = $sender;

        if (count($args) === 1 || count($args) === 3) {
            if ($sender instanceof Player) {
                $target = $sender;
            } else {
                $sender->sendMessage("§c» §fУкажите игрока!");

                return true;
            }
            if (count($args) === 1) {
                $target = $sender->getServer()->getPlayer($args[0]);
                if ($target === null) {
                    $sender->sendMessage("§c» §fИгрок не найден!");

                    return true;
                }
            }
        } else {
            $target = $sender->getServer()->getPlayer($args[0]);
            if ($target === null) {
                $sender->sendMessage("§c» §fИгрок не найден!");

                return true;
            }
            if (count($args) === 2) {
                $origin = $target;
                $target = $sender->getServer()->getPlayer($args[1]);
                if ($target === null) {
                    $sender->sendMessage("§c» §fИгрок не найден!");

                    return true;
                }
            }
        }

        if (count($args) < 3) {
            $origin->teleport($target);
            Command::broadcastCommandMessage($sender, new TranslationContainer("commands.tp.success", [$origin->getName(), $target->getName()]));

            return true;
        } elseif ($target->getLevel() !== null) {
            if (count($args) === 4 || count($args) === 6) {
                $pos = 1;
            } else {
                $pos = 0;
            }

            $x = $this->getRelativeDouble($target->x, $sender, $args[$pos++]);
            $y = $this->getRelativeDouble($target->y, $sender, $args[$pos++], 0, 128);
            $z = $this->getRelativeDouble($target->z, $sender, $args[$pos++]);
            $yaw = $target->getYaw();
            $pitch = $target->getPitch();

            if (count($args) === 6 || (count($args) === 5 && $pos === 3)) {
                $yaw = $args[$pos++];
                $pitch = $args[$pos++];
            }

            $target->teleport(new Vector3($x, $y, $z), $yaw, $pitch);
            Command::broadcastCommandMessage($sender, new TranslationContainer("commands.tp.success.coordinates", [$target->getName(), round($x, 2), round($y, 2), round($z, 2)]));

            return true;
        }

        $sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

        return true;
    }
}
