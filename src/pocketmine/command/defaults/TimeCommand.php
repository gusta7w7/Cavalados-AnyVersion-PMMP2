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
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

use function count;

class TimeCommand extends VanillaCommand
{
    public function __construct($name)
    {
        parent::__construct(
            $name,
            "%pocketmine.command.time.description",
            "%pocketmine.command.time.usage"
        );
        $this->setPermission("pocketmine.command.time.add;pocketmine.command.time.set;pocketmine.command.time.start;pocketmine.command.time.stop");
    }

    public function execute(CommandSender $sender, $currentAlias, array $args)
    {
        if (count($args) < 1) {
            $sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

            return false;
        }

        if ($args[0] === "start") {
            if (!$sender->hasPermission("pocketmine.command.time.start")) {
                $sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));

                return true;
            }
            foreach ($sender->getServer()->getLevels() as $level) {
                $level->checkTime();
                $level->startTime();
                $level->checkTime();
            }
            Command::broadcastCommandMessage($sender, "§a» §fВремя перезапущено!");
            return true;
        } elseif ($args[0] === "stop") {
            if (!$sender->hasPermission("pocketmine.command.time.stop")) {
                $sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));

                return true;
            }
            foreach ($sender->getServer()->getLevels() as $level) {
                $level->checkTime();
                $level->stopTime();
                $level->checkTime();
            }
            Command::broadcastCommandMessage($sender, "§a» §fВремя остановлено!");
            return true;
        } elseif ($args[0] === "query") {
            if (!$sender->hasPermission("pocketmine.command.time.query")) {
                $sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));

                return true;
            }
            if ($sender instanceof Player) {
                $level = $sender->getLevel();
            } else {
                $level = $sender->getServer()->getDefaultLevel();
            }
            $sender->sendMessage(new TranslationContainer("commands.time.query", [$level->getTime()]));
            return true;
        }


        if (count($args) < 2) {
            $sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

            return false;
        }

        if ($args[0] === "set") {
            if (!$sender->hasPermission("pocketmine.command.time.set")) {
                $sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));

                return true;
            }

            if ($args[1] === "day") {
                $value = 0;
            } elseif ($args[1] === "night") {
                $value = Level::TIME_NIGHT;
            } else {
                $value = $this->getInteger($sender, $args[1], 0);
            }

            foreach ($sender->getServer()->getLevels() as $level) {
                $level->checkTime();
                $level->setTime($value);
                $level->checkTime();
            }
            Command::broadcastCommandMessage($sender, new TranslationContainer("commands.time.set", [$value]));
        } elseif ($args[0] === "add") {
            if (!$sender->hasPermission("pocketmine.command.time.add")) {
                $sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));

                return true;
            }

            $value = $this->getInteger($sender, $args[1], 0);
            foreach ($sender->getServer()->getLevels() as $level) {
                $level->checkTime();
                $level->setTime($level->getTime() + $value);
                $level->checkTime();
            }
            Command::broadcastCommandMessage($sender, new TranslationContainer("commands.time.added", [$value]));
        } else {
            $sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
        }

        return true;
    }
}
