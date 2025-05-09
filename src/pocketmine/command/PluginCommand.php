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

use pocketmine\event\TranslationContainer;
use pocketmine\plugin\Plugin;

class PluginCommand extends Command implements PluginIdentifiableCommand
{
    /** @var Plugin */
    private $owningPlugin;

    /** @var CommandExecutor */
    private $executor;

    /**
     * @param string $name
     * @param Plugin $owner
     */
    public function __construct($name, Plugin $owner)
    {
        parent::__construct($name);
        $this->owningPlugin = $owner;
        $this->executor = $owner;
        $this->usageMessage = "";
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {

        if (!$this->owningPlugin->isEnabled()) {
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        $success = $this->executor->onCommand($sender, $this, $commandLabel, $args);

        if (!$success && $this->usageMessage !== "") {
            $sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
        }

        return $success;
    }

    public function getExecutor()
    {
        return $this->executor;
    }

    /**
     * @param CommandExecutor $executor
     */
    public function setExecutor(CommandExecutor $executor)
    {
        $this->executor = ($executor != null) ? $executor : $this->owningPlugin;
    }

    /**
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->owningPlugin;
    }
}
