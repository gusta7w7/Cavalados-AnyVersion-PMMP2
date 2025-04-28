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
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GamemodeCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.gamemode.description",
			"%commands.gamemode.usage",
			["gm"]
		);
		$this->setPermission("pocketmine.command.gamemode");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) === 0){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

			return false;
		}

		$gameMode = (int) Server::getGamemodeFromString($args[0]);

		if($gameMode === -1){
			$sender->sendMessage("§c» §fНеизвестный режим игры!");

			return true;
		}

		$target = $sender;
		if(isset($args[1])){
			$target = $sender->getServer()->getPlayer($args[1]);
			if($target === null){
				$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));

				return true;
			}
		}elseif(!($sender instanceof Player)){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

			return true;
		}

		if($target->setGamemode($gameMode) == false){
			$sender->sendMessage("§c» §fСмена режима игры для§e " . $target->getName() . " §fне удалась!");
		}else{
			if($target === $sender){
				Command::broadcastCommandMessage($sender, new TranslationContainer("commands.gamemode.success.self", [Server::getGamemodeString($gameMode)]));
			}else{
				$target->sendMessage(new TranslationContainer("gameMode.changed"));
				Command::broadcastCommandMessage($sender, new TranslationContainer("commands.gamemode.success.other", [$target->getName(), Server::getGamemodeString($gameMode)]));
			}
		}

		return true;
	}
}
