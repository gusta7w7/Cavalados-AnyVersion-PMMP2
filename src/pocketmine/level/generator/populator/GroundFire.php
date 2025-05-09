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

namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

class GroundFire extends Populator
{
    /** @var ChunkManager */
    private $level;
    private $randomAmount;
    private $baseAmount;

    public function setRandomAmount($amount)
    {
        $this->randomAmount = $amount;
    }

    public function setBaseAmount($amount)
    {
        $this->baseAmount = $amount;
    }

    public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random)
    {
        $this->level = $level;
        $amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
        for ($i = 0; $i < $amount; ++$i) {
            $x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
            $z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
            $y = $this->getHighestWorkableBlock($x, $z);
            //echo "Fire to $x, $y, $z\n";
            if ($y !== -1 && $this->canGroundFireStay($x, $y, $z)) {
                $this->level->setBlockIdAt($x, $y, $z, Block::FIRE);
                $this->level->updateBlockLight($x, $y, $z);
            }
        }
    }

    private function canGroundFireStay($x, $y, $z)
    {
        $b = $this->level->getBlockIdAt($x, $y, $z);
        return ($b === Block::AIR || $b === Block::SNOW_LAYER) && $this->level->getBlockIdAt($x, $y - 1, $z) === 87;
    }

    private function getHighestWorkableBlock($x, $z)
    {
        for ($y = 0; $y <= 127; ++$y) {
            $b = $this->level->getBlockIdAt($x, $y, $z);
            if ($b == Block::AIR) {
                break;
            }
        }

        return $y === 0 ? -1 : $y;
    }
}
