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
use pocketmine\block\Sapling;
use pocketmine\level\generator\populator\MossStone;
use pocketmine\level\generator\populator\Tree;

class TaigaBiome extends SnowyBiome
{
    public function __construct()
    {
        parent::__construct();

        $trees = new Tree(Sapling::SPRUCE);
        $trees->setBaseAmount(10);
        $this->addPopulator($trees);

        $mossStone = new MossStone();
        $mossStone->setBaseAmount(1);

        $this->addPopulator($mossStone);

        $this->setElevation(63, 81);

        $this->temperature = 0.05;
        $this->rainfall = 0.8;

        $this->setGroundCover([
            Block::get(Block::PODZOL, 0),
            Block::get(Block::DIRT, 0),
            Block::get(Block::DIRT, 0),
            Block::get(Block::DIRT, 0)
        ]);
    }

    public function getName() : string
    {
        return "Taiga";
    }
}
