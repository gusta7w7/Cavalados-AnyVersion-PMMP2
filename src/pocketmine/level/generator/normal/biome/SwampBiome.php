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

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Block;
use pocketmine\block\Flower as FlowerBlock;
use pocketmine\level\generator\populator\Flower;
use pocketmine\level\generator\populator\LilyPad;

class SwampBiome extends GrassyBiome
{
    public function __construct()
    {
        parent::__construct();

        $flower = new Flower();
        $flower->setBaseAmount(8);
        $flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_BLUE_ORCHID]);

        $this->addPopulator($flower);

        $lilypad = new LilyPad();
        $lilypad->setBaseAmount(4);
        $this->addPopulator($lilypad);

        $this->setElevation(62, 63);

        $this->temperature = 0.8;
        $this->rainfall = 0.9;
    }

    public function getName() : string
    {
        return "Swamp";
    }

    public function getColor()
    {
        return 0x6a7039;
    }
}
