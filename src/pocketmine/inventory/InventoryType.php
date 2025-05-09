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

namespace pocketmine\inventory;

use function count;

/**
 * Saves all the information regarding default inventory sizes and types
 */
class InventoryType
{
    const CHEST = 0;
    const DOUBLE_CHEST = 1;
    const PLAYER = 2;
    const FURNACE = 3;
    const CRAFTING = 4;
    const WORKBENCH = 5;
    //const STONECUTTER = 6;
    const BREWING_STAND = 7;
    const ANVIL = 8;
    const ENCHANT_TABLE = 9;
    const DISPENSER = 10;
    const DROPPER = 11;
    const HOPPER = 12;

    const PLAYER_FLOATING = 254;

    private static $default = [];

    private $size;
    private $title;
    private $typeId;

    /**
     * @param $index
     *
     * @return InventoryType
     */
    public static function get($index)
    {
        return static::$default[$index] ?? null;
    }

    public static function init()
    {
        if (count(static::$default) > 0) {
            return;
        }

        static::$default[static::CHEST] = new InventoryType(27, "Chest", 0);
        static::$default[static::DOUBLE_CHEST] = new InventoryType(27 + 27, "Double Chest", 0);
        static::$default[static::PLAYER] = new InventoryType(36 + 4, "Player", 0); //36 CONTAINER, 4 ARMOR
        static::$default[static::FURNACE] = new InventoryType(3, "Furnace", 2);
        static::$default[static::CRAFTING] = new InventoryType(5, "Crafting", 1); //4 CRAFTING slots, 1 RESULT
        static::$default[static::WORKBENCH] = new InventoryType(10, "Crafting", 1); //9 CRAFTING slots, 1 RESULT
        static::$default[static::ENCHANT_TABLE] = new InventoryType(2, "Enchant", 3); //1 INPUT/OUTPUT, 1 LAPIS
        static::$default[static::BREWING_STAND] = new InventoryType(4, "Brewing", 4); //1 INPUT, 3 POTION
        static::$default[static::ANVIL] = new InventoryType(3, "Anvil", 5); //2 INPUT, 1 OUTPUT
        static::$default[static::DISPENSER] = new InventoryType(9, "Dispenser", 6); //9 CONTAINER
        static::$default[static::DROPPER] = new InventoryType(9, "Dropper", 7); //9 CONTAINER
        static::$default[static::HOPPER] = new InventoryType(5, "Hopper", 8); //5 CONTAINER

        static::$default[static::PLAYER_FLOATING] = new InventoryType(36, "Floating", null); //Mirror all slots of main inventory (needed for large item pickups)
    }

    /**
     * @param int    $defaultSize
     * @param string $defaultTitle
     * @param int    $typeId
     */
    private function __construct($defaultSize, $defaultTitle, $typeId = 0)
    {
        $this->size = $defaultSize;
        $this->title = $defaultTitle;
        $this->typeId = $typeId;
    }

    /**
     * @return int
     */
    public function getDefaultSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getDefaultTitle()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getNetworkType()
    {
        return $this->typeId;
    }
}
