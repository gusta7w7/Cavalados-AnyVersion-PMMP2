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

namespace pocketmine\level\generator\object;

use pocketmine\block\Block;
use pocketmine\block\Sapling;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

use function abs;

abstract class Tree
{
    public $overridable = [
        Block::AIR        => true,
        6                 => true,
        17                => true,
        18                => true,
        Block::SNOW_LAYER => true,
        Block::LOG2       => true,
        Block::LEAVES2    => true
    ];

    public $type = 0;
    public $trunkBlock = Block::LOG;
    public $leafBlock = Block::LEAVES;
    public $treeHeight = 7;
    public $leafType = 0;

    public static function growTree(ChunkManager $level, $x, $y, $z, Random $random, $type = 0, bool $noBigTree = true)
    {
        switch ($type) {
            case Sapling::SPRUCE:
                $tree = new SpruceTree();
                break;
            case Sapling::BIRCH:
                if ($random->nextBoundedInt(39) === 0) {
                    $tree = new BirchTree(true);
                } else {
                    $tree = new BirchTree();
                }
                break;
            case Sapling::JUNGLE:
                $tree = new JungleTree();
                break;
            case Sapling::ACACIA:
                $tree = new AcaciaTree();
                break;
            case Sapling::DARK_OAK:
                $tree = new DarkOakTree();
                break;
            case Sapling::OAK:
            default:
                if (!$noBigTree && $random->nextRange(0, 9) === 0) {
                    $tree = new BigTree();
                } else {
                    $tree = new OakTree();
                }
                break;
        }
        if ($tree->canPlaceObject($level, $x, $y, $z, $random)) {
            $tree->placeObject($level, $x, $y, $z, $random);
        }
    }

    public function canPlaceObject(ChunkManager $level, $x, $y, $z, Random $random)
    {
        $radiusToCheck = 0;
        for ($yy = 0; $yy < $this->treeHeight + 3; ++$yy) {
            if ($yy == 1 || $yy === $this->treeHeight) {
                ++$radiusToCheck;
            }
            for ($xx = -$radiusToCheck; $xx < ($radiusToCheck + 1); ++$xx) {
                for ($zz = -$radiusToCheck; $zz < ($radiusToCheck + 1); ++$zz) {
                    if (!isset($this->overridable[$level->getBlockIdAt($x + $xx, $y + $yy, $z + $zz)])) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function placeObject(ChunkManager $level, $x, $y, $z, Random $random)
    {

        $this->placeTrunk($level, $x, $y, $z, $random, $this->treeHeight - 1);

        for ($yy = $y - 3 + $this->treeHeight; $yy <= $y + $this->treeHeight; ++$yy) {
            $yOff = $yy - ($y + $this->treeHeight);
            $mid = (int) (1 - $yOff / 2);
            for ($xx = $x - $mid; $xx <= $x + $mid; ++$xx) {
                $xOff = abs($xx - $x);
                for ($zz = $z - $mid; $zz <= $z + $mid; ++$zz) {
                    $zOff = abs($zz - $z);
                    if ($xOff === $mid && $zOff === $mid && ($yOff === 0 || $random->nextBoundedInt(2) === 0)) {
                        continue;
                    }
                    if (!Block::$solid[$level->getBlockIdAt($xx, $yy, $zz)]) {
                        $level->setBlockIdAt($xx, $yy, $zz, $this->leafBlock);
                        $level->setBlockDataAt($xx, $yy, $zz, $this->leafType);
                    }
                }
            }
        }
    }

    protected function placeTrunk(ChunkManager $level, $x, $y, $z, Random $random, $trunkHeight)
    {
        // The base dirt block
        $level->setBlockIdAt($x, $y - 1, $z, Block::DIRT);

        for ($yy = 0; $yy < $trunkHeight; ++$yy) {
            $blockId = $level->getBlockIdAt($x, $y + $yy, $z);
            if (isset($this->overridable[$blockId])) {
                $level->setBlockIdAt($x, $y + $yy, $z, $this->trunkBlock);
                $level->setBlockDataAt($x, $y + $yy, $z, $this->type);
            }
        }
    }
}
