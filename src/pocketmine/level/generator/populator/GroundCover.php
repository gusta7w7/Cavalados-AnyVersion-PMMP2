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
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\Level;
use pocketmine\level\SimpleChunkManager;
use pocketmine\utils\Random;

use function count;
use function min;
use function ord;

class GroundCover extends Populator
{
    public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random)
    {
        $chunk = $level->getChunk($chunkX, $chunkZ);
        if ($level instanceof Level || $level instanceof SimpleChunkManager) {
            $waterHeight = $level->getWaterHeight();
        } else {
            $waterHeight = 0;
        }
        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                $biome = Biome::getBiome($chunk->getBiomeId($x, $z));
                $cover = $biome->getGroundCover();
                if (count($cover) > 0) {
                    $diffY = 0;
                    if (!$cover[0]->isSolid()) {
                        $diffY = 1;
                    }

                    $column = $chunk->getBlockIdColumn($x, $z);
                    for ($y = 127; $y > 0; --$y) {
                        if ($column[$y] !== "\x00" && !Block::get(ord($column[$y]))->isTransparent()) {
                            break;
                        }
                    }
                    $startY = min(127, $y + $diffY);
                    $endY = $startY - count($cover);
                    for ($y = $startY; $y > $endY && $y >= 0; --$y) {
                        $b = $cover[$startY - $y];
                        if ($column[$y] === "\x00" && $b->isSolid()) {
                            break;
                        }
                        if ($y <= $waterHeight && $b->getId() == Block::GRASS && $chunk->getBlockId($x, $y + 1, $z) == Block::STILL_WATER) {
                            $b = Block::get(Block::DIRT);
                        }
                        if ($b->getDamage() === 0) {
                            $chunk->setBlockId($x, $y, $z, $b->getId());
                        } else {
                            $chunk->setBlock($x, $y, $z, $b->getId(), $b->getDamage());
                        }
                    }
                }
            }
        }
    }
}
