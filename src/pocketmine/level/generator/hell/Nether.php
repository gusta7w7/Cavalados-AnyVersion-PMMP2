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

namespace pocketmine\level\generator\hell;

use pocketmine\block\Block;
use pocketmine\block\Gravel;
use pocketmine\block\Lava;
use pocketmine\block\NetherQuartzOre;
use pocketmine\block\SoulSand;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\generator\biome\BiomeSelector;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\level\generator\object\OreType;
use pocketmine\level\generator\populator\GroundFire;
use pocketmine\level\generator\populator\NetherGlowStone;
use pocketmine\level\generator\populator\NetherLava;
use pocketmine\level\generator\populator\NetherOre;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3 as Vector3;
use pocketmine\utils\Random;

use function abs;
use function exp;

class Nether extends Generator
{
    /** @var Populator[] */
    private $populators = [];
    /** @var ChunkManager */
    private $level;
    /** @var Random */
    private $random;
    private $waterHeight = 32;
    private $emptyHeight = 64;
    private $emptyAmplitude = 1;
    private $density = 0.5;
    private $bedrockDepth = 5;

    /** @var Populator[] */
    private $generationPopulators = [];
    /** @var Simplex */
    private $noiseBase;

    /** @var BiomeSelector */
    private $selector;

    private static $GAUSSIAN_KERNEL = null;
    private static $SMOOTH_SIZE = 2;

    public function __construct(array $options = [])
    {
        if (self::$GAUSSIAN_KERNEL === null) {
            self::generateKernel();
        }
    }

    private static function generateKernel()
    {
        self::$GAUSSIAN_KERNEL = [];

        $bellSize = 1 / self::$SMOOTH_SIZE;
        $bellHeight = 2 * self::$SMOOTH_SIZE;

        for ($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; ++$sx) {
            self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] = [];

            for ($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; ++$sz) {
                $bx = $bellSize * $sx;
                $bz = $bellSize * $sz;
                self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE][$sz + self::$SMOOTH_SIZE] = $bellHeight * exp(-($bx * $bx + $bz * $bz) / 2);
            }
        }
    }

    public function getName() : string
    {
        return "Nether";
    }

    public function getWaterHeight() : int
    {
        return $this->waterHeight;
    }

    public function getSettings()
    {
        return [];
    }

    public function init(ChunkManager $level, Random $random)
    {
        $this->level = $level;
        $this->random = $random;
        $this->random->setSeed($this->level->getSeed());
        $this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 64);
        $this->random->setSeed($this->level->getSeed());

        $ores = new NetherOre();
        $ores->setOreTypes([
            new OreType(new NetherQuartzOre(), 20, 16, 0, 128),
            new OreType(new SoulSand(), 5, 64, 0, 128),
            new OreType(new Gravel(), 5, 64, 0, 128),
            new OreType(new Lava(), 1, 16, 0, $this->waterHeight),
        ]);
        $this->populators[] = $ores;
        $this->populators[] = new NetherGlowStone();
        $groundFire = new GroundFire();
        $groundFire->setBaseAmount(1);
        $groundFire->setRandomAmount(1);
        $this->populators[] = $groundFire;
        $lava = new NetherLava();
        $lava->setBaseAmount(0);
        $lava->setRandomAmount(0);
        $this->populators[] = $lava;
    }

    public function generateChunk($chunkX, $chunkZ)
    {
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

        $noise = Generator::getFastNoise3D($this->noiseBase, 16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

        $chunk = $this->level->getChunk($chunkX, $chunkZ);

        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {

                $biome = Biome::getBiome(Biome::HELL);
                $chunk->setBiomeId($x, $z, $biome->getId());
                $color = [0, 0, 0];
                $bColor = $biome->getColor();
                $color[0] += (($bColor >> 16) ** 2);
                $color[1] += ((($bColor >> 8) & 0xff) ** 2);
                $color[2] += (($bColor & 0xff) ** 2);

                $chunk->setBiomeColor($x, $z, $color[0], $color[1], $color[2]);

                for ($y = 0; $y < 128; ++$y) {
                    if ($y === 0 || $y === 127) {
                        $chunk->setBlockId($x, $y, $z, Block::BEDROCK);
                        continue;
                    }
                    $noiseValue = (abs($this->emptyHeight - $y) / $this->emptyHeight) * $this->emptyAmplitude - $noise[$x][$z][$y];
                    $noiseValue -= 1 - $this->density;

                    if ($noiseValue > 0) {
                        $chunk->setBlockId($x, $y, $z, Block::NETHERRACK);
                    } elseif ($y <= $this->waterHeight) {
                        $chunk->setBlockId($x, $y, $z, Block::STILL_LAVA);
                        $chunk->setBlockLight($x, $y + 1, $z, 15);
                    }
                }
            }
        }

        foreach ($this->generationPopulators as $populator) {
            $populator->populate($this->level, $chunkX, $chunkZ, $this->random);
        }
    }

    public function populateChunk($chunkX, $chunkZ)
    {
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
        foreach ($this->populators as $populator) {
            $populator->populate($this->level, $chunkX, $chunkZ, $this->random);
        }

        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        $biome = Biome::getBiome($chunk->getBiomeId(7, 7));
        $biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
    }

    public function getSpawn()
    {
        return new Vector3(127.5, 128, 127.5);
    }
}
