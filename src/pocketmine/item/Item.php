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

/**
 * All the Item classes
 */

namespace pocketmine\item;

use pocketmine\block\Anvil;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\block\Block;
use pocketmine\block\Fence;
use pocketmine\block\Flower;
use pocketmine\entity\CaveSpider;
use pocketmine\entity\Entity;
use pocketmine\entity\PigZombie;
use pocketmine\entity\Silverfish;
use pocketmine\entity\Skeleton;
use pocketmine\entity\Spider;
use pocketmine\entity\Witch;
use pocketmine\entity\Zombie;
use pocketmine\inventory\CreativeItems;
use pocketmine\inventory\Fuel;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\level\Level;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\Config;

class Item implements ItemIds
{
    /** @var NBT */
    private static $cachedParser = null;

    private static function parseCompoundTag(string $tag): CompoundTag
    {
        if (self::$cachedParser === null) {
            self::$cachedParser = new NBT(NBT::LITTLE_ENDIAN);
        }

        self::$cachedParser->read($tag);
        return self::$cachedParser->getData();
    }

    private static function writeCompoundTag(CompoundTag $tag): string
    {
        if (self::$cachedParser === null) {
            self::$cachedParser = new NBT(NBT::LITTLE_ENDIAN);
        }

        self::$cachedParser->setData($tag);
        return self::$cachedParser->write();
    }


    /** @var \SplFixedArray */
    public static $list = null;
    protected $block;
    protected $id;
    protected $meta;
    private $tags = "";
    private $cachedNBT = null;
    public $count;
    protected $durability = 0;
    protected $name;

    public function canBeActivated(): bool
    {
        return false;
    }

    public static function init($readFromJson = false)
    {
        if (self::$list === null) {
            //TODO: Sort this mess into some kind of order
            self::$list = new \SplFixedArray(65536);
            self::$list[self::SUGARCANE] = Sugarcane::class;
            self::$list[self::WHEAT_SEEDS] = WheatSeeds::class;
            self::$list[self::PUMPKIN_SEEDS] = PumpkinSeeds::class;
            self::$list[self::MELON_SEEDS] = MelonSeeds::class;
            self::$list[self::MUSHROOM_STEW] = MushroomStew::class;
            self::$list[self::RABBIT_STEW] = RabbitStew::class;
            self::$list[self::BEETROOT_SOUP] = BeetrootSoup::class;
            self::$list[self::CARROT] = Carrot::class;
            self::$list[self::POTATO] = Potato::class;
            self::$list[self::BEETROOT_SEEDS] = BeetrootSeeds::class;
            self::$list[self::SIGN] = Sign::class;
            self::$list[self::WOODEN_DOOR] = WoodenDoor::class;
            self::$list[self::SPRUCE_DOOR] = SpruceDoor::class;
            self::$list[self::BIRCH_DOOR] = BirchDoor::class;
            self::$list[self::JUNGLE_DOOR] = JungleDoor::class;
            self::$list[self::ACACIA_DOOR] = AcaciaDoor::class;
            self::$list[self::DARK_OAK_DOOR] = DarkOakDoor::class;
            self::$list[self::BUCKET] = Bucket::class;
            self::$list[self::IRON_DOOR] = IronDoor::class;
            self::$list[self::CAKE] = Cake::class;
            self::$list[self::BED] = Bed::class;
            self::$list[self::PAINTING] = Painting::class;
            self::$list[self::COAL] = Coal::class;
            self::$list[self::APPLE] = Apple::class;
            self::$list[self::SPAWN_EGG] = SpawnEgg::class;
            self::$list[self::DIAMOND] = Diamond::class;
            self::$list[self::STICK] = Stick::class;
            self::$list[self::SNOWBALL] = Snowball::class;
            self::$list[self::BOWL] = Bowl::class;
            self::$list[self::FEATHER] = Feather::class;
            self::$list[self::BRICK] = Brick::class;
            self::$list[self::LEATHER_CAP] = LeatherCap::class;
            self::$list[self::LEATHER_TUNIC] = LeatherTunic::class;
            self::$list[self::LEATHER_PANTS] = LeatherPants::class;
            self::$list[self::LEATHER_BOOTS] = LeatherBoots::class;
            self::$list[self::CHAIN_HELMET] = ChainHelmet::class;
            self::$list[self::CHAIN_CHESTPLATE] = ChainChestplate::class;
            self::$list[self::CHAIN_LEGGINGS] = ChainLeggings::class;
            self::$list[self::CHAIN_BOOTS] = ChainBoots::class;
            self::$list[self::IRON_HELMET] = IronHelmet::class;
            self::$list[self::IRON_CHESTPLATE] = IronChestplate::class;
            self::$list[self::IRON_LEGGINGS] = IronLeggings::class;
            self::$list[self::IRON_BOOTS] = IronBoots::class;
            self::$list[self::GOLD_HELMET] = GoldHelmet::class;
            self::$list[self::GOLD_CHESTPLATE] = GoldChestplate::class;
            self::$list[self::GOLD_LEGGINGS] = GoldLeggings::class;
            self::$list[self::GOLD_BOOTS] = GoldBoots::class;
            self::$list[self::DIAMOND_HELMET] = DiamondHelmet::class;
            self::$list[self::DIAMOND_CHESTPLATE] = DiamondChestplate::class;
            self::$list[self::DIAMOND_LEGGINGS] = DiamondLeggings::class;
            self::$list[self::DIAMOND_BOOTS] = DiamondBoots::class;
            self::$list[self::IRON_SWORD] = IronSword::class;
            self::$list[self::IRON_INGOT] = IronIngot::class;
            self::$list[self::GOLD_INGOT] = GoldIngot::class;
            self::$list[self::IRON_SHOVEL] = IronShovel::class;
            self::$list[self::IRON_PICKAXE] = IronPickaxe::class;
            self::$list[self::IRON_AXE] = IronAxe::class;
            self::$list[self::IRON_HOE] = IronHoe::class;
            self::$list[self::DIAMOND_SWORD] = DiamondSword::class;
            self::$list[self::DIAMOND_SHOVEL] = DiamondShovel::class;
            self::$list[self::DIAMOND_PICKAXE] = DiamondPickaxe::class;
            self::$list[self::DIAMOND_AXE] = DiamondAxe::class;
            self::$list[self::DIAMOND_HOE] = DiamondHoe::class;
            self::$list[self::GOLD_SWORD] = GoldSword::class;
            self::$list[self::GOLD_SHOVEL] = GoldShovel::class;
            self::$list[self::GOLD_PICKAXE] = GoldPickaxe::class;
            self::$list[self::GOLD_AXE] = GoldAxe::class;
            self::$list[self::GOLD_HOE] = GoldHoe::class;
            self::$list[self::STONE_SWORD] = StoneSword::class;
            self::$list[self::STONE_SHOVEL] = StoneShovel::class;
            self::$list[self::STONE_PICKAXE] = StonePickaxe::class;
            self::$list[self::STONE_AXE] = StoneAxe::class;
            self::$list[self::STONE_HOE] = StoneHoe::class;
            self::$list[self::WOODEN_SWORD] = WoodenSword::class;
            self::$list[self::WOODEN_SHOVEL] = WoodenShovel::class;
            self::$list[self::WOODEN_PICKAXE] = WoodenPickaxe::class;
            self::$list[self::WOODEN_AXE] = WoodenAxe::class;
            self::$list[self::WOODEN_HOE] = WoodenHoe::class;
            self::$list[self::FLINT_STEEL] = FlintSteel::class;
            self::$list[self::SHEARS] = Shears::class;
            self::$list[self::BOW] = Bow::class;

            self::$list[self::RAW_FISH] = Fish::class;
            self::$list[self::COOKED_FISH] = CookedFish::class;

            self::$list[self::NETHER_QUARTZ] = NetherQuartz::class;
            self::$list[self::POTION] = Potion::class;
            self::$list[self::GLASS_BOTTLE] = GlassBottle::class;
            self::$list[self::SPLASH_POTION] = SplashPotion::class;
            self::$list[self::ENCHANTING_BOTTLE] = EnchantingBottle::class;
            self::$list[self::BOAT] = Boat::class;
            self::$list[self::MINECART] = Minecart::class;

            self::$list[self::ARROW] = Arrow::class;
            self::$list[self::STRING] = ItemString::class;
            self::$list[self::GUNPOWDER] = Gunpowder::class;
            self::$list[self::WHEAT] = Wheat::class;
            self::$list[self::BREAD] = Bread::class;
            self::$list[self::FLINT] = Flint::class;
            self::$list[self::FLINT] = Flint::class;
            self::$list[self::RAW_PORKCHOP] = RawPorkchop::class;
            self::$list[self::COOKED_PORKCHOP] = CookedPorkchop::class;
            self::$list[self::GOLDEN_APPLE] = GoldenApple::class;
            self::$list[self::MINECART] = Minecart::class;
            self::$list[self::REDSTONE] = Redstone::class;
            self::$list[self::LEATHER] = Leather::class;
            self::$list[self::CLAY] = Clay::class;
            self::$list[self::PAPER] = Paper::class;
            self::$list[self::BOOK] = Book::class;
            self::$list[self::SLIMEBALL] = Slimeball::class;
            self::$list[self::EGG] = Egg::class;
            self::$list[self::COMPASS] = Compass::class;
            self::$list[self::CLOCK] = Clock::class;
            self::$list[self::GLOWSTONE_DUST] = GlowstoneDust::class;
            self::$list[self::DYE] = Dye::class;
            self::$list[self::BONE] = Bone::class;
            self::$list[self::SUGAR] = Sugar::class;
            self::$list[self::COOKIE] = Cookie::class;
            self::$list[self::MELON] = Melon::class;
            self::$list[self::RAW_BEEF] = RawBeef::class;
            self::$list[self::STEAK] = Steak::class;
            self::$list[self::RAW_CHICKEN] = RawChicken::class;
            self::$list[self::COOKED_CHICKEN] = CookedChicken::class;
            self::$list[self::GOLD_NUGGET] = GoldNugget::class;
            self::$list[self::EMERALD] = Emerald::class;
            self::$list[self::BAKED_POTATO] = BakedPotato::class;
            self::$list[self::PUMPKIN_PIE] = PumpkinPie::class;
            self::$list[self::NETHER_BRICK] = NetherBrick::class;
            self::$list[self::QUARTZ] = Quartz::class;
            self::$list[self::BREWING_STAND] = BrewingStand::class;
            self::$list[self::CAMERA] = Camera::class;
            self::$list[self::BEETROOT] = Beetroot::class;
            self::$list[self::FLOWER_POT] = FlowerPot::class;
            self::$list[self::SKULL] = Skull::class;
            self::$list[self::RAW_RABBIT] = RawRabbit::class;
            self::$list[self::COOKED_RABBIT] = CookedRabbit::class;
            self::$list[self::GOLDEN_CARROT] = GoldenCarrot::class;
            self::$list[self::NETHER_WART] = NetherWart::class;
            self::$list[self::SPIDER_EYE] = SpiderEye::class;
            self::$list[self::FERMENTED_SPIDER_EYE] = FermentedSpiderEye::class;
            self::$list[self::BLAZE_POWDER] = BlazePowder::class;
            self::$list[self::MAGMA_CREAM] = MagmaCream::class;
            self::$list[self::GLISTERING_MELON] = GlisteringMelon::class;
            self::$list[self::ITEM_FRAME] = ItemFrame::class;
            self::$list[self::ENCHANTED_BOOK] = EnchantedBook::class;
            self::$list[self::REPEATER] = Repeater::class;
            self::$list[self::CAULDRON] = Cauldron::class;
            self::$list[self::ROTTEN_FLESH] = RottenFlesh::class;
            self::$list[self::ENCHANTED_GOLDEN_APPLE] = EnchantedGoldenApple::class;
            self::$list[self::RAW_MUTTON] = RawMutton::class;
            self::$list[self::COOKED_MUTTON] = CookedMutton::class;
            self::$list[self::HOPPER] = Hopper::class;

            for ($i = 0; $i < 256; ++$i) {
                if (Block::$list[$i] !== null) {
                    self::$list[$i] = Block::$list[$i];
                }
            }
        }

        self::initCreativeItems($readFromJson);
    }

    private static $creative = [];
    private static $p70creative = [];

    private static function initCreativeItems($readFromJson = false)
    {
        self::clearCreativeItems();
        self::buildingTab();
        self::decorationTab();
        self::toolsTab();
        self::seedsTab();
        if (!$readFromJson) {
            foreach (CreativeItems::ITEMS as $category) {
                foreach ($category as $itemData) {
                    if (!isset($itemData["meta"])) {
                        $itemData["meta"] = 0;
                    }
                    $item = Item::get($itemData["id"], @$itemData["meta"]);
                    if (isset($itemData["ench"])) {
                        foreach ($itemData["ench"] as $ench) {
                            $item->addEnchantment(Enchantment::getEnchantment($ench["id"])->setLevel($ench["lvl"]));
                        }
                    }
                    self::addCreativeItem($item);
                }
            }
        } else {
            $creativeItems = new Config(Server::getInstance()->getFilePath() . "src/pocketmine/resources/creativeitems.json", Config::JSON, []);
            foreach ($creativeItems->getAll() as $item) {
                self::addCreativeItem(Item::get($item["ID"], $item["Damage"]));
            }
        }

    }


    private static function buildingTab()
    {

        Item::addp70CreativeItem(Item::get(Item::COBBLESTONE, 0));
        Item::addp70CreativeItem(Item::get(Item::STONE_BRICKS, 0));
        Item::addp70CreativeItem(Item::get(Item::STONE_BRICKS, 1));
        Item::addp70CreativeItem(Item::get(Item::STONE_BRICKS, 2));
        Item::addp70CreativeItem(Item::get(Item::STONE_BRICKS, 3));
        Item::addp70CreativeItem(Item::get(Item::MOSS_STONE, 0));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_PLANKS, 0));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_PLANKS, 1));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_PLANKS, 2));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_PLANKS, 3));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_PLANKS, 4));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_PLANKS, 5));
        Item::addp70CreativeItem(Item::get(Item::BRICKS, 0));
        Item::addp70CreativeItem(Item::get(Item::STONE, 0));
        Item::addp70CreativeItem(Item::get(Item::STONE, 1));
        Item::addp70CreativeItem(Item::get(Item::STONE, 2));
        Item::addp70CreativeItem(Item::get(Item::STONE, 3));
        Item::addp70CreativeItem(Item::get(Item::STONE, 4));
        Item::addp70CreativeItem(Item::get(Item::STONE, 5));
        Item::addp70CreativeItem(Item::get(Item::STONE, 6));
        Item::addp70CreativeItem(Item::get(Item::DIRT, 0));
        Item::addp70CreativeItem(Item::get(Item::PODZOL, 0));
        Item::addp70CreativeItem(Item::get(Item::GRASS, 0));
        Item::addp70CreativeItem(Item::get(Item::MYCELIUM, 0));
        Item::addp70CreativeItem(Item::get(Item::CLAY_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::HARDENED_CLAY, 0));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 0));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 1));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 2));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 3));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 4));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 5));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 6));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 7));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 8));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 9));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 10));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 11));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 12));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 13));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 14));
        Item::addp70CreativeItem(Item::get(Item::STAINED_CLAY, 15));
        Item::addp70CreativeItem(Item::get(Item::SANDSTONE, 0));
        Item::addp70CreativeItem(Item::get(Item::SANDSTONE, 1));
        Item::addp70CreativeItem(Item::get(Item::SANDSTONE, 2));
        Item::addp70CreativeItem(Item::get(Item::RED_SANDSTONE, 0));
        Item::addp70CreativeItem(Item::get(Item::RED_SANDSTONE, 1));
        Item::addp70CreativeItem(Item::get(Item::RED_SANDSTONE, 2));
        Item::addp70CreativeItem(Item::get(Item::SAND, 0));
        Item::addp70CreativeItem(Item::get(Item::SAND, 1));
        Item::addp70CreativeItem(Item::get(Item::GRAVEL, 0));
        Item::addp70CreativeItem(Item::get(Item::TRUNK, 0));
        Item::addp70CreativeItem(Item::get(Item::TRUNK, 1));
        Item::addp70CreativeItem(Item::get(Item::TRUNK, 2));
        Item::addp70CreativeItem(Item::get(Item::TRUNK, 3));
        Item::addp70CreativeItem(Item::get(Item::TRUNK2, 0));
        Item::addp70CreativeItem(Item::get(Item::TRUNK2, 1));
        Item::addp70CreativeItem(Item::get(Item::NETHER_BRICKS, 0));
        Item::addp70CreativeItem(Item::get(Item::NETHERRACK, 0));
        Item::addp70CreativeItem(Item::get(Item::SOUL_SAND, 0));
        Item::addp70CreativeItem(Item::get(Item::BEDROCK, 0));
        Item::addp70CreativeItem(Item::get(Item::COBBLESTONE_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::OAK_WOODEN_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::SPRUCE_WOODEN_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::BIRCH_WOODEN_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::JUNGLE_WOODEN_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::ACACIA_WOODEN_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::DARK_OAK_WOODEN_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::BRICK_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::SANDSTONE_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::RED_SANDSTONE_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::STONE_BRICK_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::NETHER_BRICKS_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::QUARTZ_STAIRS, 0));
        Item::addp70CreativeItem(Item::get(Item::SLAB, 0));
        Item::addp70CreativeItem(Item::get(Item::SLAB, 3));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_SLAB, 0));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_SLAB, 1));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_SLAB, 2));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_SLAB, 3));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_SLAB, 4));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_SLAB, 5));
        Item::addp70CreativeItem(Item::get(Item::SLAB, 4));
        Item::addp70CreativeItem(Item::get(Item::SLAB, 1));
        Item::addp70CreativeItem(Item::get(Item::STONE_SLAB, 0));
        Item::addp70CreativeItem(Item::get(Item::SLAB, 5));
        Item::addp70CreativeItem(Item::get(Item::SLAB, 6));
        Item::addp70CreativeItem(Item::get(Item::SLAB, 7));
        Item::addp70CreativeItem(Item::get(Item::QUARTZ_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::QUARTZ_BLOCK, 1));
        Item::addp70CreativeItem(Item::get(Item::QUARTZ_BLOCK, 2));
        Item::addp70CreativeItem(Item::get(Item::COAL_ORE, 0));
        Item::addp70CreativeItem(Item::get(Item::IRON_ORE, 0));
        Item::addp70CreativeItem(Item::get(Item::GOLD_ORE, 0));
        Item::addp70CreativeItem(Item::get(Item::DIAMOND_ORE, 0));
        Item::addp70CreativeItem(Item::get(Item::LAPIS_ORE, 0));
        Item::addp70CreativeItem(Item::get(Item::REDSTONE_ORE, 0));
        Item::addp70CreativeItem(Item::get(Item::EMERALD_ORE, 0));
        Item::addp70CreativeItem(Item::get(Item::NETHER_QUARTZ_ORE, 0));
        Item::addp70CreativeItem(Item::get(Item::OBSIDIAN, 0));
        Item::addp70CreativeItem(Item::get(Item::ICE, 0));
        Item::addp70CreativeItem(Item::get(Item::PACKED_ICE, 0));
        Item::addp70CreativeItem(Item::get(Item::SNOW_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::END_STONE, 0));
    }

    private static function decorationTab()
    {
        //Decoration
        Item::addp70CreativeItem(Item::get(Item::COBBLESTONE_WALL, 0));
        Item::addp70CreativeItem(Item::get(Item::COBBLESTONE_WALL, 1));
        Item::addp70CreativeItem(Item::get(Item::WATER_LILY, 0));
        Item::addp70CreativeItem(Item::get(Item::GOLD_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::IRON_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::DIAMOND_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::LAPIS_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::COAL_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::EMERALD_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::REDSTONE_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::SNOW_LAYER, 0));
        Item::addp70CreativeItem(Item::get(Item::GLASS, 0));
        Item::addp70CreativeItem(Item::get(Item::GLOWSTONE_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::VINES, 0));
        Item::addp70CreativeItem(Item::get(Item::NETHER_REACTOR, 0));
        Item::addp70CreativeItem(Item::get(Item::LADDER, 0));
        Item::addp70CreativeItem(Item::get(Item::SPONGE, 0));
        Item::addp70CreativeItem(Item::get(Item::GLASS_PANE, 0));
        Item::addp70CreativeItem(Item::get(Item::OAK_DOOR, 0));
        Item::addp70CreativeItem(Item::get(Item::SPRUCE_DOOR, 0));
        Item::addp70CreativeItem(Item::get(Item::BIRCH_DOOR, 0));
        Item::addp70CreativeItem(Item::get(Item::JUNGLE_DOOR, 0));
        Item::addp70CreativeItem(Item::get(Item::ACACIA_DOOR, 0));
        Item::addp70CreativeItem(Item::get(Item::DARK_OAK_DOOR, 0));
        Item::addp70CreativeItem(Item::get(Item::IRON_DOOR, 0));
        Item::addp70CreativeItem(Item::get(Item::TRAPDOOR, 0));
        Item::addp70CreativeItem(Item::get(Item::IRON_TRAPDOOR, 0));
        Item::addp70CreativeItem(Item::get(Item::FENCE, Fence::FENCE_OAK));
        Item::addp70CreativeItem(Item::get(Item::FENCE, Fence::FENCE_SPRUCE));
        Item::addp70CreativeItem(Item::get(Item::FENCE, Fence::FENCE_BIRCH));
        Item::addp70CreativeItem(Item::get(Item::FENCE, Fence::FENCE_JUNGLE));
        Item::addp70CreativeItem(Item::get(Item::FENCE, Fence::FENCE_ACACIA));
        Item::addp70CreativeItem(Item::get(Item::FENCE, Fence::FENCE_DARKOAK));
        Item::addp70CreativeItem(Item::get(Item::NETHER_BRICK_FENCE, 0));
        Item::addp70CreativeItem(Item::get(Item::FENCE_GATE, 0));
        Item::addp70CreativeItem(Item::get(Item::FENCE_GATE_BIRCH, 0));
        Item::addp70CreativeItem(Item::get(Item::FENCE_GATE_SPRUCE, 0));
        Item::addp70CreativeItem(Item::get(Item::FENCE_GATE_DARK_OAK, 0));
        Item::addp70CreativeItem(Item::get(Item::FENCE_GATE_JUNGLE, 0));
        Item::addp70CreativeItem(Item::get(Item::FENCE_GATE_ACACIA, 0));
        Item::addp70CreativeItem(Item::get(Item::IRON_BARS, 0));
        Item::addp70CreativeItem(Item::get(Item::BED, 0));
        Item::addp70CreativeItem(Item::get(Item::BOOKSHELF, 0));
        Item::addp70CreativeItem(Item::get(Item::SIGN, 0));
        Item::addp70CreativeItem(Item::get(Item::PAINTING, 0));
        Item::addp70CreativeItem(Item::get(Item::ITEM_FRAME, 0));
        Item::addp70CreativeItem(Item::get(Item::WORKBENCH, 0));
        Item::addp70CreativeItem(Item::get(Item::STONECUTTER, 0));
        Item::addp70CreativeItem(Item::get(Item::CHEST, 0));
        Item::addp70CreativeItem(Item::get(Item::TRAPPED_CHEST, 0));
        Item::addp70CreativeItem(Item::get(Item::FURNACE, 0));
        Item::addp70CreativeItem(Item::get(Item::BREWING_STAND, 0));
        Item::addp70CreativeItem(Item::get(Item::CAULDRON, 0));
        Item::addp70CreativeItem(Item::get(Item::NOTEBLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::END_PORTAL_FRAME, 0));
        Item::addp70CreativeItem(Item::get(Item::ANVIL, ANVIL::NORMAL));
        Item::addp70CreativeItem(Item::get(Item::ANVIL, ANVIL::SLIGHTLY_DAMAGED));
        Item::addp70CreativeItem(Item::get(Item::ANVIL, ANVIL::VERY_DAMAGED));
        Item::addp70CreativeItem(Item::get(Item::DANDELION, 0));
        Item::addp70CreativeItem(Item::get(Item::RED_FLOWER, Flower::TYPE_POPPY));
        Item::addp70CreativeItem(Item::get(Item::RED_FLOWER, Flower::TYPE_BLUE_ORCHID));
        Item::addp70CreativeItem(Item::get(Item::RED_FLOWER, Flower::TYPE_ALLIUM));
        Item::addp70CreativeItem(Item::get(Item::RED_FLOWER, Flower::TYPE_AZURE_BLUET));
        Item::addp70CreativeItem(Item::get(Item::RED_FLOWER, Flower::TYPE_RED_TULIP));
        Item::addp70CreativeItem(Item::get(Item::RED_FLOWER, Flower::TYPE_ORANGE_TULIP));
        Item::addp70CreativeItem(Item::get(Item::RED_FLOWER, Flower::TYPE_WHITE_TULIP));
        Item::addp70CreativeItem(Item::get(Item::RED_FLOWER, Flower::TYPE_PINK_TULIP));
        Item::addp70CreativeItem(Item::get(Item::RED_FLOWER, Flower::TYPE_OXEYE_DAISY));

        Item::addp70CreativeItem(Item::get(Item::DOUBLE_PLANT, 0)); // SUNFLOWER ?
        Item::addp70CreativeItem(Item::get(Item::DOUBLE_PLANT, 1)); // Lilac ?
        Item::addp70CreativeItem(Item::get(Item::DOUBLE_PLANT, 2)); // Double TALL_GRASS
        Item::addp70CreativeItem(Item::get(Item::DOUBLE_PLANT, 3)); // Large fern
        Item::addp70CreativeItem(Item::get(Item::DOUBLE_PLANT, 4)); // Rose bush
        Item::addp70CreativeItem(Item::get(Item::DOUBLE_PLANT, 5)); // Peony

        Item::addp70CreativeItem(Item::get(Item::BROWN_MUSHROOM, 0));
        Item::addp70CreativeItem(Item::get(Item::RED_MUSHROOM, 0));
        Item::addp70CreativeItem(Item::get(Item::BROWN_MUSHROOM_BLOCK, 14));
        Item::addp70CreativeItem(Item::get(Item::RED_MUSHROOM_BLOCK, 14));
        Item::addp70CreativeItem(Item::get(Item::BROWN_MUSHROOM_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::RED_MUSHROOM_BLOCK, 15));
        Item::addp70CreativeItem(Item::get(Item::CACTUS, 0));
        Item::addp70CreativeItem(Item::get(Item::MELON_BLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::PUMPKIN, 0));
        Item::addp70CreativeItem(Item::get(Item::LIT_PUMPKIN, 0));
        Item::addp70CreativeItem(Item::get(Item::COBWEB, 0));
        Item::addp70CreativeItem(Item::get(Item::HAY_BALE, 0));
        Item::addp70CreativeItem(Item::get(Item::TALL_GRASS, 1)); // Grass
        Item::addp70CreativeItem(Item::get(Item::TALL_GRASS, 2)); // Fern
        Item::addp70CreativeItem(Item::get(Item::DEAD_BUSH, 0));

        Item::addp70CreativeItem(Item::get(Item::SAPLING, 0)); // Oak
        Item::addp70CreativeItem(Item::get(Item::SAPLING, 1)); // Spruce
        Item::addp70CreativeItem(Item::get(Item::SAPLING, 2)); // Birtch
        Item::addp70CreativeItem(Item::get(Item::SAPLING, 3)); // Jungle
        Item::addp70CreativeItem(Item::get(Item::SAPLING, 4)); // Acacia
        Item::addp70CreativeItem(Item::get(Item::SAPLING, 5)); // Dark oak

        Item::addp70CreativeItem(Item::get(Item::LEAVES, 0)); // Oak
        Item::addp70CreativeItem(Item::get(Item::LEAVES, 1)); // Spruce
        Item::addp70CreativeItem(Item::get(Item::LEAVES, 2)); // Birtch
        Item::addp70CreativeItem(Item::get(Item::LEAVES, 3)); // Jungle
        Item::addp70CreativeItem(Item::get(Item::LEAVES2, 0)); // Acacia
        Item::addp70CreativeItem(Item::get(Item::LEAVES2, 1)); // Dark oak

        Item::addp70CreativeItem(Item::get(Item::CAKE, 0));

        Item::addp70CreativeItem(Item::get(Item::SKULL, 0)); // Skeleton
        Item::addp70CreativeItem(Item::get(Item::SKULL, 1)); // Wither Skeleton
        Item::addp70CreativeItem(Item::get(Item::SKULL, 2)); // Zombie
        Item::addp70CreativeItem(Item::get(Item::SKULL, 3)); // Head (Steve)
        Item::addp70CreativeItem(Item::get(Item::SKULL, 4)); // Creeper

        Item::addp70CreativeItem(Item::get(Item::FLOWER_POT, 0));
        Item::addp70CreativeItem(Item::get(Item::MONSTER_SPAWNER, 0));
        Item::addp70CreativeItem(Item::get(Item::ENCHANTING_TABLE, 0));
        Item::addp70CreativeItem(Item::get(Item::SLIME_BLOCK, 0));

        Item::addp70CreativeItem(Item::get(Item::WOOL, 0));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 8));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 7));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 15));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 12));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 14));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 1));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 4));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 5));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 13));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 9));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 3));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 11));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 10));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 2));
        Item::addp70CreativeItem(Item::get(Item::WOOL, 6));


        Item::addp70CreativeItem(Item::get(Item::CARPET, 0));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 8));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 7));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 15));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 12));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 14));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 1));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 4));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 5));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 13));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 9));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 3));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 11));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 10));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 2));
        Item::addp70CreativeItem(Item::get(Item::CARPET, 6));
    }

    private static function toolsTab()
    {
        Item::addp70CreativeItem(Item::get(Item::RAIL, 0));
        Item::addp70CreativeItem(Item::get(Item::POWERED_RAIL, 0));
        Item::addp70CreativeItem(Item::get(Item::DETECTOR_RAIL, 0));
        Item::addp70CreativeItem(Item::get(Item::ACTIVATOR_RAIL, 0));
        Item::addp70CreativeItem(Item::get(Item::TORCH, 0));
        Item::addp70CreativeItem(Item::get(Item::BUCKET, 0));
        Item::addp70CreativeItem(Item::get(Item::BUCKET, 1)); // milk
        Item::addp70CreativeItem(Item::get(Item::BUCKET, 8)); // water
        Item::addp70CreativeItem(Item::get(Item::BUCKET, 10)); // lava
        Item::addp70CreativeItem(Item::get(Item::TNT, 0));
        Item::addp70CreativeItem(Item::get(Item::REDSTONE, 0));
        Item::addp70CreativeItem(Item::get(Item::BOW, 0));
        Item::addp70CreativeItem(Item::get(Item::FISHING_ROD, 0));
        Item::addp70CreativeItem(Item::get(Item::FLINT_AND_STEEL, 0));
        Item::addp70CreativeItem(Item::get(Item::SHEARS, 0));
        Item::addp70CreativeItem(Item::get(Item::CLOCK, 0));
        Item::addp70CreativeItem(Item::get(Item::COMPASS, 0));
        Item::addp70CreativeItem(Item::get(Item::MINECART, 0));
        Item::addp70CreativeItem(Item::get(Item::MINECART_WITH_CHEST, 0));
        Item::addp70CreativeItem(Item::get(Item::MINECART_WITH_HOPPER, 0));
        Item::addp70CreativeItem(Item::get(Item::MINECART_WITH_TNT, 0));
        Item::addp70CreativeItem(Item::get(Item::CAMERA, 0)); // Crashes client
        Item::addp70CreativeItem(Item::get(Item::BOAT, 0)); // Oak
        Item::addp70CreativeItem(Item::get(Item::BOAT, 1)); // Spruce
        Item::addp70CreativeItem(Item::get(Item::BOAT, 2)); // Birch
        Item::addp70CreativeItem(Item::get(Item::BOAT, 3)); // Jungle
        Item::addp70CreativeItem(Item::get(Item::BOAT, 4)); // Acacia
        Item::addp70CreativeItem(Item::get(Item::BOAT, 5)); // Dark Oak

        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 15)); //Villager
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 10)); //Chicken
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 11)); //Cow
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 12)); //Pig
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 13)); //Sheep
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 14)); //Wolf
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 22)); //Ocelot
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 16)); //Mooshroom
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 19)); //Bat
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 18)); //Rabbit
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 33)); //Creeper
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 38)); //Enderman
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 39)); //Silverfish
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 34)); //Skeleton
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 37)); //Slime
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 35)); //Spider
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 32)); //Zombie
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 36)); //Zombie Pigman
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 17)); //Squid
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 40)); //Cave spider
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 42)); //Magma Cube
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 41)); //Ghast
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 43)); //Blaze
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 20)); //Iron Golem
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 21)); //Snow Golem
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 44)); //Zombie Villager
        Item::addp70CreativeItem(Item::get(Item::SPAWN_EGG, 45)); //Witch

        Item::addp70CreativeItem(Item::get(Item::WOODEN_SWORD));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_HOE));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_SHOVEL));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_PICKAXE));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_AXE));

        Item::addp70CreativeItem(Item::get(Item::STONE_SWORD));
        Item::addp70CreativeItem(Item::get(Item::STONE_HOE));
        Item::addp70CreativeItem(Item::get(Item::STONE_SHOVEL));
        Item::addp70CreativeItem(Item::get(Item::STONE_PICKAXE));
        Item::addp70CreativeItem(Item::get(Item::STONE_AXE));
        Item::addp70CreativeItem(Item::get(Item::IRON_SWORD));
        Item::addp70CreativeItem(Item::get(Item::IRON_HOE));
        Item::addp70CreativeItem(Item::get(Item::IRON_SHOVEL));
        Item::addp70CreativeItem(Item::get(Item::IRON_PICKAXE));
        Item::addp70CreativeItem(Item::get(Item::IRON_AXE));

        Item::addp70CreativeItem(Item::get(Item::DIAMOND_SWORD));
        Item::addp70CreativeItem(Item::get(Item::DIAMOND_HOE));
        Item::addp70CreativeItem(Item::get(Item::DIAMOND_SHOVEL));
        Item::addp70CreativeItem(Item::get(Item::DIAMOND_PICKAXE));
        Item::addp70CreativeItem(Item::get(Item::DIAMOND_AXE));

        Item::addp70CreativeItem(Item::get(Item::GOLD_SWORD));
        Item::addp70CreativeItem(Item::get(Item::GOLD_HOE));
        Item::addp70CreativeItem(Item::get(Item::GOLD_SHOVEL));
        Item::addp70CreativeItem(Item::get(Item::GOLD_PICKAXE));
        Item::addp70CreativeItem(Item::get(Item::GOLD_AXE));

        Item::addp70CreativeItem(Item::get(Item::LEATHER_CAP));
        Item::addp70CreativeItem(Item::get(Item::LEATHER_TUNIC));
        Item::addp70CreativeItem(Item::get(Item::LEATHER_PANTS));
        Item::addp70CreativeItem(Item::get(Item::LEATHER_BOOTS));

        Item::addp70CreativeItem(Item::get(Item::CHAIN_HELMET));
        Item::addp70CreativeItem(Item::get(Item::CHAIN_CHESTPLATE));
        Item::addp70CreativeItem(Item::get(Item::CHAIN_LEGGINGS));
        Item::addp70CreativeItem(Item::get(Item::CHAIN_BOOTS));

        Item::addp70CreativeItem(Item::get(Item::IRON_HELMET));
        Item::addp70CreativeItem(Item::get(Item::IRON_CHESTPLATE));
        Item::addp70CreativeItem(Item::get(Item::IRON_LEGGINGS));
        Item::addp70CreativeItem(Item::get(Item::IRON_BOOTS));

        Item::addp70CreativeItem(Item::get(Item::DIAMOND_HELMET));
        Item::addp70CreativeItem(Item::get(Item::DIAMOND_CHESTPLATE));
        Item::addp70CreativeItem(Item::get(Item::DIAMOND_LEGGINGS));
        Item::addp70CreativeItem(Item::get(Item::DIAMOND_BOOTS));

        Item::addp70CreativeItem(Item::get(Item::GOLD_HELMET));
        Item::addp70CreativeItem(Item::get(Item::GOLD_CHESTPLATE));
        Item::addp70CreativeItem(Item::get(Item::GOLD_LEGGINGS));
        Item::addp70CreativeItem(Item::get(Item::GOLD_BOOTS));
        Item::addp70CreativeItem(Item::get(Item::LEVER));
        Item::addp70CreativeItem(Item::get(Item::REDSTONE_LAMP));
        Item::addp70CreativeItem(Item::get(Item::REDSTONE_TORCH));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_PRESSURE_PLATE));
        Item::addp70CreativeItem(Item::get(Item::STONE_PRESSURE_PLATE));
        Item::addp70CreativeItem(Item::get(Item::LIGHT_WEIGHTED_PRESSURE_PLATE));
        Item::addp70CreativeItem(Item::get(Item::HEAVY_WEIGHTED_PRESSURE_PLATE));
        Item::addp70CreativeItem(Item::get(Item::WOODEN_BUTTON, 5));
        Item::addp70CreativeItem(Item::get(Item::STONE_BUTTON, 5));
        Item::addp70CreativeItem(Item::get(Item::DAYLIGHT_SENSOR));
        Item::addp70CreativeItem(Item::get(Item::TRIPWIRE_HOOK));
        Item::addp70CreativeItem(Item::get(Item::REPEATER));
        Item::addp70CreativeItem(Item::get(Item::COMPARATOR));
        Item::addp70CreativeItem(Item::get(Item::DISPENSER, 3));
        Item::addp70CreativeItem(Item::get(Item::DROPPER, 3));
        Item::addp70CreativeItem(Item::get(Item::HOPPER));
        Item::addp70CreativeItem(Item::get(Item::SNOWBALL));
    }

    private static function seedsTab()
    {
        //Seeds
        /*
        Im gonna make it so you can do:
        Item::addCreativeItem(Item::get(Item::ENCHANTED_BOOK, EnchantedBook::'ENCHANTMENT'));
        */
        Item::addp70CreativeItem(Item::get(Item::COAL, 0));
        Item::addp70CreativeItem(Item::get(Item::COAL, 1)); // charcoal
        Item::addp70CreativeItem(Item::get(Item::DIAMOND, 0));
        Item::addp70CreativeItem(Item::get(Item::IRON_INGOT, 0));
        Item::addp70CreativeItem(Item::get(Item::GOLD_INGOT, 0));
        Item::addp70CreativeItem(Item::get(Item::EMERALD, 0));
        Item::addp70CreativeItem(Item::get(Item::STICK, 0));
        Item::addp70CreativeItem(Item::get(Item::BOWL, 0));
        Item::addp70CreativeItem(Item::get(Item::STRING, 0));
        Item::addp70CreativeItem(Item::get(Item::FEATHER, 0));
        Item::addp70CreativeItem(Item::get(Item::FLINT, 0));
        Item::addp70CreativeItem(Item::get(Item::LEATHER, 0));
        Item::addp70CreativeItem(Item::get(Item::RABBIT_HIDE, 0));
        Item::addp70CreativeItem(Item::get(Item::CLAY, 0));
        Item::addp70CreativeItem(Item::get(Item::SUGAR, 0));
        Item::addp70CreativeItem(Item::get(Item::NETHER_QUARTZ, 0));
        Item::addp70CreativeItem(Item::get(Item::PAPER, 0));
        Item::addp70CreativeItem(Item::get(Item::BOOK, 0));
        Item::addp70CreativeItem(Item::get(Item::ARROW, 0));
        Item::addp70CreativeItem(Item::get(Item::BONE, 0));
        Item::addp70CreativeItem(Item::get(Item::EMPTY_MAP, 0));
        Item::addp70CreativeItem(Item::get(Item::SUGARCANE, 0));
        Item::addp70CreativeItem(Item::get(Item::WHEAT, 0));
        Item::addp70CreativeItem(Item::get(Item::SEEDS, 0));
        Item::addp70CreativeItem(Item::get(Item::PUMPKIN_SEEDS, 0));
        Item::addp70CreativeItem(Item::get(Item::MELON_SEEDS, 0));
        Item::addp70CreativeItem(Item::get(Item::BEETROOT_SEEDS, 0));
        Item::addp70CreativeItem(Item::get(Item::EGG, 0));
        Item::addp70CreativeItem(Item::get(Item::APPLE, 0));
        Item::addp70CreativeItem(Item::get(Item::GOLDEN_APPLE, 0));
        Item::addp70CreativeItem(Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0)); // Enchanted golden apple
        Item::addp70CreativeItem(Item::get(Item::RAW_FISH, 0));
        Item::addp70CreativeItem(Item::get(Item::RAW_SALMON, 0)); // Salmon
        Item::addp70CreativeItem(Item::get(Item::CLOWN_FISH, 0)); // Clownfish
        Item::addp70CreativeItem(Item::get(Item::PUFFER_FISH, 0)); // Pufferfish
        Item::addp70CreativeItem(Item::get(Item::COOKED_FISH, 0));
        Item::addp70CreativeItem(Item::get(Item::COOKED_SALMON, 0)); // Salmon
        Item::addp70CreativeItem(Item::get(Item::ROTTEN_FLESH, 0));
        Item::addp70CreativeItem(Item::get(Item::MUSHROOM_STEW, 0));
        Item::addp70CreativeItem(Item::get(Item::BREAD, 0));
        Item::addp70CreativeItem(Item::get(Item::RAW_PORKCHOP, 0));
        Item::addp70CreativeItem(Item::get(Item::COOKED_PORKCHOP, 0));
        Item::addp70CreativeItem(Item::get(Item::RAW_CHICKEN, 0));
        Item::addp70CreativeItem(Item::get(Item::COOKED_CHICKEN, 0));
        Item::addp70CreativeItem(Item::get(Item::RAW_BEEF, 0));
        Item::addp70CreativeItem(Item::get(Item::COOKED_BEEF, 0));
        Item::addp70CreativeItem(Item::get(Item::MELON, 0));
        Item::addp70CreativeItem(Item::get(Item::CARROT, 0));
        Item::addp70CreativeItem(Item::get(Item::POTATO, 0));
        Item::addp70CreativeItem(Item::get(Item::BAKED_POTATO, 0));
        Item::addp70CreativeItem(Item::get(Item::POISONOUS_POTATO, 0));
        Item::addp70CreativeItem(Item::get(Item::BEETROOT, 0));
        Item::addp70CreativeItem(Item::get(Item::COOKIE, 0));
        Item::addp70CreativeItem(Item::get(Item::PUMPKIN_PIE, 0));
        Item::addp70CreativeItem(Item::get(Item::RAW_RABBIT, 0));
        Item::addp70CreativeItem(Item::get(Item::COOKED_RABBIT, 0));
        Item::addp70CreativeItem(Item::get(Item::RABBIT_STEW, 0));
        Item::addp70CreativeItem(Item::get(Item::MAGMA_CREAM, 0));
        Item::addp70CreativeItem(Item::get(Item::BLAZE_ROD, 0));
        Item::addp70CreativeItem(Item::get(Item::GOLD_NUGGET, 0));
        Item::addp70CreativeItem(Item::get(Item::GOLDEN_CARROT, 0));
        Item::addp70CreativeItem(Item::get(Item::GLISTERING_MELON, 0));
        Item::addp70CreativeItem(Item::get(Item::RABBIT_FOOT, 0));
        Item::addp70CreativeItem(Item::get(Item::GHAST_TEAR, 0));
        Item::addp70CreativeItem(Item::get(Item::SLIMEBALL, 0));
        Item::addp70CreativeItem(Item::get(Item::BLAZE_POWDER, 0));
        Item::addp70CreativeItem(Item::get(Item::NETHER_WART, 0));
        Item::addp70CreativeItem(Item::get(Item::GUNPOWDER, 0));
        Item::addp70CreativeItem(Item::get(Item::GLOWSTONE_DUST, 0));
        Item::addp70CreativeItem(Item::get(Item::SPIDER_EYE, 0));
        Item::addp70CreativeItem(Item::get(Item::FERMENTED_SPIDER_EYE, 0));
        Item::addp70CreativeItem(Item::get(Item::BOTTLE_O_ENCHANTING, 0));

        for ($i = 0; $i < 79; $i++) {
            $item = Item::get(Item::ENCHANTED_BOOK)->addEnchantment(Enchantment::getEnchantment($i));
            if ($item !== \null) Item::addCreativeItem();
            else Item::addp70CreativeItem(Item::get(Item::ENCHANTED_BOOK));
        }

        Item::addp70CreativeItem(Item::get(Item::DYE, 0));
        Item::addp70CreativeItem(Item::get(Item::DYE, 8));
        Item::addp70CreativeItem(Item::get(Item::DYE, 7));
        Item::addp70CreativeItem(Item::get(Item::DYE, 15));
        Item::addp70CreativeItem(Item::get(Item::DYE, 12));
        Item::addp70CreativeItem(Item::get(Item::DYE, 14));
        Item::addp70CreativeItem(Item::get(Item::DYE, 1));
        Item::addp70CreativeItem(Item::get(Item::DYE, 4));
        Item::addp70CreativeItem(Item::get(Item::DYE, 5));
        Item::addp70CreativeItem(Item::get(Item::DYE, 13));
        Item::addp70CreativeItem(Item::get(Item::DYE, 9));
        Item::addp70CreativeItem(Item::get(Item::DYE, 3));
        Item::addp70CreativeItem(Item::get(Item::DYE, 11));
        Item::addp70CreativeItem(Item::get(Item::DYE, 10));
        Item::addp70CreativeItem(Item::get(Item::DYE, 2));
        Item::addp70CreativeItem(Item::get(Item::DYE, 6));
        Item::addp70CreativeItem(Item::get(Item::GLASS_BOTTLE, 0));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::WATER_BOTTLE));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::AWKWARD));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::THICK));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::MUNDANE_EXTENDED));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::MUNDANE));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::NIGHT_VISION));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::NIGHT_VISION_T));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::INVISIBILITY));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::INVISIBILITY_T));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::LEAPING));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::LEAPING_T));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::LEAPING_TWO));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::FIRE_RESISTANCE));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::FIRE_RESISTANCE_T));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::SWIFTNESS));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::SWIFTNESS_T));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::SWIFTNESS_TWO));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::SLOWNESS));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::SLOWNESS_T));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::WATER_BREATHING));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::WATER_BREATHING_T));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::HEALING));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::HEALING_TWO));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::HARMING));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::HARMING_TWO));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::POISON));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::POISON_T));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::POISON_TWO));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::REGENERATION));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::REGENERATION_T));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::REGENERATION_TWO));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::STRENGTH));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::STRENGTH_T));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::STRENGTH_TWO));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::WEAKNESS));
        self::addp70CreativeItem(Item::get(Item::POTION, Potion::WEAKNESS_T));

        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::WATER_BOTTLE));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::AWKWARD));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::THICK));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::MUNDANE_EXTENDED));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::MUNDANE));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::NIGHT_VISION));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::NIGHT_VISION_T));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::INVISIBILITY));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::INVISIBILITY_T));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::LEAPING));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::LEAPING_T));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::LEAPING_TWO));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::FIRE_RESISTANCE));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::FIRE_RESISTANCE_T));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::SWIFTNESS));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::SWIFTNESS_T));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::SWIFTNESS_TWO));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::SLOWNESS));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::SLOWNESS_T));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::WATER_BREATHING));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::WATER_BREATHING_T));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::HEALING));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::HEALING_TWO));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::HARMING));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::HARMING_TWO));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::POISON));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::POISON_T));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::POISON_TWO));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::REGENERATION));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::REGENERATION_T));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::REGENERATION_TWO));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::STRENGTH));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::STRENGTH_T));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::STRENGTH_TWO));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::WEAKNESS));
        self::addp70CreativeItem(Item::get(Item::SPLASH_POTION, Potion::WEAKNESS_T));
    }

    public static function clearCreativeItems()
    {
        Item::$creative = [];
    }

    public static function getCreativeItems(): array
    {
        return Item::$creative;
    }

    public static function getp70CreativeItems(): array
    {
        return Item::$p70creative;
    }

    public static function addCreativeItem(Item $item)
    {
        Item::$creative[] = $item;
    }

    public static function addp70CreativeItem(Item $item)
    {
        Item::$p70creative[] = $item;
    }

    public static function removeCreativeItem(Item $item)
    {
        $index = self::getCreativeItemIndex($item);
        if ($index !== -1) {
            unset(Item::$creative[$index]);
        }
    }

    public static function isCreativeItem(Item $item): bool
    {
        foreach (Item::$creative as $i => $d) {
            if ($item->equals($d, !$item->isTool())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $index
     * @return Item
     */
    public static function getCreativeItem(int $index)
    {
        return isset(Item::$creative[$index]) ? Item::$creative[$index] : null;
    }

    public static function getCreativeItemIndex(Item $item): int
    {
        foreach (Item::$creative as $i => $d) {
            if ($item->equals($d, !$item->isTool())) {
                return $i;
            }
        }

        return -1;
    }

    public static function get($id, $meta = 0, int $count = 1, $tags = ""): Item
    {
        try {
            if (is_string($id)) {
                $item = Item::fromString($id);
                $item->setCount($count);
                $item->setDamage($meta);
                return $item;
            }
            $class = self::$list[$id];
            if ($class === null) {
                return (new Item($id, $meta, $count))->setCompoundTag($tags);
            } elseif ($id < 256) {
                return (new ItemBlock(new $class($meta), $meta, $count))->setCompoundTag($tags);
            } else {
                return (new $class($meta, $count))->setCompoundTag($tags);
            }
        } catch (\RuntimeException $e) {
            return (new Item($id, $meta, $count))->setCompoundTag($tags);
        }
    }

    /**
     * @param string $str
     * @param bool $multiple
     * @return Item[]|Item
     */
    public static function fromString(string $str, bool $multiple = false)
    {
        if ($multiple === true) {
            $blocks = [];
            foreach (explode(",", $str) as $b) {
                $blocks[] = self::fromString($b, false);
            }

            return $blocks;
        } else {
            $b = explode(":", str_replace([" ", "minecraft:"], ["_", ""], trim($str)));
            if (!isset($b[1])) {
                $meta = 0;
            } else {
                $meta = $b[1] & 0xFFFF;
            }

            if (defined(Item::class . "::" . strtoupper($b[0]))) {
                $item = self::get(constant(Item::class . "::" . strtoupper($b[0])), $meta);
                if ($item->getId() === self::AIR and strtoupper($b[0]) !== "AIR") {
                    $item = self::get($b[0] & 0xFFFF, $meta);
                }
            } else {
                $item = self::get($b[0] & 0xFFFF, $meta);
            }

            return $item;
        }
    }

    public function __construct($id, $meta = 0, int $count = 1, string $name = "Unknown")
    {
        if (is_string($id)) {
            $item = Item::fromString($id);
            $id = $item->getId();
            if ($item->getDamage() != $meta) $meta = $item->getDamage();
            $name = $item->getName();
        }
        $this->id = $id & 0xffff;
        $this->meta = $meta !== null ? $meta & 0xffff : null;
        $this->count = $count;
        $this->name = $name;
        if (!isset($this->block) and $this->id <= 0xff and isset(Block::$list[$this->id])) {
            $this->block = Block::get($this->id, $this->meta);
            $this->name = $this->block->getName();
        }
    }

    public function setCompoundTag($tags)
    {
        if ($tags instanceof CompoundTag) {
            $this->setNamedTag($tags);
        } else {
            $this->tags = $tags;
            $this->cachedNBT = null;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCompoundTag()
    {
        return $this->tags;
    }

    public function hasCompoundTag(): bool
    {
        return $this->tags !== "" and $this->tags !== null;
    }

    public function hasCustomBlockData(): bool
    {
        if (!$this->hasCompoundTag()) {
            return false;
        }

        $tag = $this->getNamedTag();
        if (isset($tag->BlockEntityTag) and $tag->BlockEntityTag instanceof CompoundTag) {
            return true;
        }


        return false;
    }

    public function clearCustomBlockData()
    {
        if (!$this->hasCompoundTag()) {
            return $this;
        }
        $tag = $this->getNamedTag();

        if (isset($tag->BlockEntityTag) and $tag->BlockEntityTag instanceof CompoundTag) {
            unset($tag->display->BlockEntityTag);
            $this->setNamedTag($tag);
        }

        return $this;
    }

    public function setCustomBlockData(CompoundTag $compound)
    {
        $tags = clone $compound;
        $tags->setName("BlockEntityTag");

        if (!$this->hasCompoundTag()) {
            $tag = new CompoundTag("", []);
        } else {
            $tag = $this->getNamedTag();
        }

        $tag->BlockEntityTag = $tags;
        $this->setNamedTag($tag);

        return $this;
    }

    public function getCustomBlockData()
    {
        if (!$this->hasCompoundTag()) {
            return null;
        }

        $tag = $this->getNamedTag();
        if (isset($tag->BlockEntityTag) and $tag->BlockEntityTag instanceof CompoundTag) {
            return $tag->BlockEntityTag;
        }

        return null;
    }

    public function hasEnchantments(): bool
    {
        if (!$this->hasCompoundTag()) {
            return false;
        }

        $tag = $this->getNamedTag();
        if (isset($tag->ench)) {
            $tag = $tag->ench;
            if ($tag instanceof ListTag) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return Enchantment|null
     */
    public function getEnchantment(int $id)
    {
        if (!$this->hasEnchantments()) {
            return null;
        }

        foreach ($this->getNamedTag()->ench as $entry) {
            if ($entry["id"] === $id) {
                $e = Enchantment::getEnchantment($entry["id"]);
                $e->setLevel($entry["lvl"]);
                return $e;
            }
        }

        return null;
    }

    /**
     * @param int $id
     * @param int $level
     * @param bool $compareLevel
     * @return bool
     */
    public function hasEnchantment(int $id, int $level = 1, bool $compareLevel = false): bool
    {
        if ($this->hasEnchantments()) {
            foreach ($this->getEnchantments() as $enchantment) {
                if ($enchantment->getId() == $id) {
                    if ($compareLevel) {
                        if ($enchantment->getLevel() == $level) {
                            return true;
                        }
                    } else {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $id
     * @return Int level|0(for null)
     */
    public function getEnchantmentLevel(int $id)
    {
        if (!$this->hasEnchantments()) {
            return 0;
        }

        foreach ($this->getNamedTag()->ench as $entry) {
            if ($entry["id"] === $id) {
                $e = Enchantment::getEnchantment($entry["id"]);
                $e->setLevel($entry["lvl"]);
                $E_level = $e->getLevel() > Enchantment::getEnchantMaxLevel($id) ? Enchantment::getEnchantMaxLevel($id) : $e->getLevel();
                return $E_level;
            }
        }

        return 0;
    }

    /**
     * @param Enchantment $ench
     */
    public function addEnchantment(Enchantment $ench)
    {
        if (!$this->hasCompoundTag()) {
            $tag = new CompoundTag("", []);
        } else {
            $tag = $this->getNamedTag();
        }

        if (!isset($tag->ench)) {
            $tag->ench = new ListTag("ench", []);
            $tag->ench->setTagType(NBT::TAG_Compound);
        }

        $found = false;

        foreach ($tag->ench as $k => $entry) {
            if ($entry["id"] === $ench->getId()) {
                $tag->ench->{$k} = new CompoundTag("", [
                    "id" => new ShortTag("id", $ench->getId()),
                    "lvl" => new ShortTag("lvl", $ench->getLevel())
                ]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            $count = 0;
            foreach ($tag->ench as $key => $value) {
                if (is_numeric($key)) {
                    $count++;
                }
            }
            $tag->ench->{$count + 1} = new CompoundTag("", [
                "id" => new ShortTag("id", $ench->getId()),
                "lvl" => new ShortTag("lvl", $ench->getLevel())
            ]);
        }

        $this->setNamedTag($tag);
    }

    /**
     * @return Enchantment[]
     */
    public function getEnchantments(): array
    {
        if (!$this->hasEnchantments()) {
            return [];
        }

        $enchantments = [];

        foreach ($this->getNamedTag()->ench as $entry) {
            $e = Enchantment::getEnchantment($entry["id"]);
            $e->setLevel($entry["lvl"]);
            $enchantments[] = $e;
        }

        return $enchantments;
    }

    public function hasRepairCost(): bool
    {
        if (!$this->hasCompoundTag()) {
            return false;
        }

        $tag = $this->getNamedTag();
        if (isset($tag->RepairCost)) {
            $tag = $tag->RepairCost;
            if ($tag instanceof IntTag) {
                return true;
            }
        }

        return false;
    }

    public function getRepairCost(): int
    {
        if (!$this->hasCompoundTag()) {
            return 1;
        }

        $tag = $this->getNamedTag();
        if (isset($tag->display)) {
            $tag = $tag->RepairCost;
            if ($tag instanceof IntTag) {
                return $tag->getValue();
            }
        }

        return 1;
    }


    public function setRepairCost(int $cost)
    {
        if ($cost === 1) {
            $this->clearRepairCost();
        }

        if (!($hadCompoundTag = $this->hasCompoundTag())) {
            $tag = new CompoundTag("", []);
        } else {
            $tag = $this->getNamedTag();
        }

        $tag->RepairCost = new IntTag("RepairCost", $cost);

        if (!$hadCompoundTag) {
            $this->setCompoundTag($tag);
        }

        return $this;
    }

    public function clearRepairCost()
    {
        if (!$this->hasCompoundTag()) {
            return $this;
        }
        $tag = $this->getNamedTag();

        if (isset($tag->RepairCost) and $tag->RepairCost instanceof IntTag) {
            unset($tag->RepairCost);
            $this->setNamedTag($tag);
        }

        return $this;
    }


    public function hasCustomName(): bool
    {
        if (!$this->hasCompoundTag()) {
            return false;
        }

        $tag = $this->getNamedTag();
        if (isset($tag->display)) {
            $tag = $tag->display;
            if ($tag instanceof CompoundTag and isset($tag->Name) and $tag->Name instanceof StringTag) {
                return true;
            }
        }

        return false;
    }

    public function getCustomName(): string
    {
        if (!$this->hasCompoundTag()) {
            return "";
        }

        $tag = $this->getNamedTag();
        if (isset($tag->display)) {
            $tag = $tag->display;
            if ($tag instanceof CompoundTag and isset($tag->Name) and $tag->Name instanceof StringTag) {
                return $tag->Name->getValue();
            }
        }

        return "";
    }

    public function setCustomName(string $name)
    {
        if ($name === "") {
            $this->clearCustomName();
        }

        if (!($hadCompoundTag = $this->hasCompoundTag())) {
            $tag = new CompoundTag("", []);
        } else {
            $tag = $this->getNamedTag();
        }

        if (isset($tag->display) and $tag->display instanceof CompoundTag) {
            $tag->display->Name = new StringTag("Name", $name);
        } else {
            $tag->display = new CompoundTag("display", [
                "Name" => new StringTag("Name", $name)
            ]);
        }

        if (!$hadCompoundTag) {
            $this->setCompoundTag($tag);
        }

        return $this;
    }

    public function clearCustomName()
    {
        if (!$this->hasCompoundTag()) {
            return $this;
        }
        $tag = $this->getNamedTag();

        if (isset($tag->display) and $tag->display instanceof CompoundTag) {
            unset($tag->display->Name);
            if ($tag->display->getCount() === 0) {
                unset($tag->display);
            }

            $this->setNamedTag($tag);
        }

        return $this;
    }

    public function getNamedTagEntry($name)
    {
        $tag = $this->getNamedTag();
        if ($tag !== null) {
            return isset($tag->{$name}) ? $tag->{$name} : null;
        }

        return null;
    }

    public function getNamedTag()
    {
        if (!$this->hasCompoundTag()) {
            return null;
        } elseif ($this->cachedNBT !== null) {
            return $this->cachedNBT;
        }
        return $this->cachedNBT = self::parseCompoundTag($this->tags);
    }

    public function setNamedTag(CompoundTag $tag)
    {
        if ($tag->getCount() === 0) {
            return $this->clearNamedTag();
        }

        $this->cachedNBT = $tag;
        $this->tags = self::writeCompoundTag($tag);

        return $this;
    }

    public function clearNamedTag()
    {
        return $this->setCompoundTag("");
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count)
    {
        $this->count = $count;
    }

    final public function getName(): string
    {
        return $this->hasCustomName() ? $this->getCustomName() : $this->name;
    }

    final public function canBePlaced(): bool
    {
        return $this->block !== null and $this->block->canBePlaced();
    }

    final public function isPlaceable(): bool
    {
        return $this->canBePlaced();
    }

    public function canBeConsumed(): bool
    {
        return false;
    }

    public function canBeConsumedBy(Entity $entity): bool
    {
        return $this->canBeConsumed();
    }

    public function onConsume(Entity $entity)
    {
    }

    public function getBlock(): Block
    {
        if ($this->block instanceof Block) {
            return clone $this->block;
        } else {
            return Block::get(self::AIR);
        }
    }

    final public function getId(): int
    {
        return $this->id;
    }

    final public function getDamage()
    {
        return $this->meta;
    }

    public function setDamage($meta)
    {
        $this->meta = $meta !== null ? $meta & 0xFFFF : null;
    }

    public function getMaxStackSize(): int
    {
        return 64;
    }

    final public function getFuelTime()
    {
        if (!isset(Fuel::$duration[$this->id])) {
            return null;
        }
        if ($this->id !== self::BUCKET or $this->meta === 10) {
            return Fuel::$duration[$this->id];
        }

        return null;
    }

    /**
     * @param Entity|Block $object
     *
     * @return bool
     */
    public function useOn($object)
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isTool()
    {
        return false;
    }

    /**
     * @return int|bool
     */
    public function getMaxDurability()
    {
        return false;
    }

    public function isPickaxe()
    {
        return false;
    }

    public function isAxe()
    {
        return false;
    }

    public function isSword()
    {
        return false;
    }

    public function isShovel()
    {
        return false;
    }

    public function isHoe()
    {
        return false;
    }

    public function isShears()
    {
        return false;
    }

    public function isArmor()
    {
        return false;
    }

    public function getArmorValue()
    {
        return false;
    }

    public function isBoots()
    {
        return false;
    }

    public function isHelmet()
    {
        return false;
    }

    public function isLeggings()
    {
        return false;
    }

    public function isChestplate()
    {
        return false;
    }

    public function getAttackDamage()
    {
        return 1;
    }

    public function getModifyAttackDamage(Entity $target)
    {
        $rec = $this->getAttackDamage();
        $sharpL = $this->getEnchantmentLevel(Enchantment::TYPE_WEAPON_SHARPNESS);
        if ($sharpL > 0) {
            $rec += 0.5 * ($sharpL + 1);
        }

        if ($target instanceof Skeleton or $target instanceof Zombie or
            $target instanceof Witch or $target instanceof PigZombie) {
            //SMITE    wither skeletons
            $rec += 2.5 * $this->getEnchantmentLevel(Enchantment::TYPE_WEAPON_SMITE);

        } elseif ($target instanceof Spider or $target instanceof CaveSpider or
            $target instanceof Silverfish) {
            //Bane of Arthropods    wither skeletons
            $rec += 2.5 * $this->getEnchantmentLevel(Enchantment::TYPE_WEAPON_ARTHROPODS);

        }
        return $rec;
    }

    final public function __toString()
    { //Get error here..
        return "Item " . $this->name . " (" . $this->id . ":" . ($this->meta === null ? "?" : $this->meta) . ")x" . $this->count . ($this->hasCompoundTag() ? " tags:0x" . bin2hex($this->getCompoundTag()) : "");
    }

    public function getDestroySpeed(Block $block, Player $player)
    {
        return 1;
    }

    public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz)
    {
        return false;
    }

    public final function equals(Item $item, bool $checkDamage = true, bool $checkCompound = true, bool $checkCount = false): bool
    {
        return $this->id === $item->getId() and ($checkCount === false or $this->getCount() === $item->getCount()) and ($checkDamage === false or $this->getDamage() === $item->getDamage()) and ($checkCompound === false or $this->getCompoundTag() === $item->getCompoundTag());
    }

    public final function deepEquals(Item $item, bool $checkDamage = true, bool $checkCompound = true, bool $checkCount = false): bool
    {
        if ($this->equals($item, $checkDamage, $checkCompound, $checkCount)) {
            return true;
        } elseif ($item->hasCompoundTag() and $this->hasCompoundTag()) {
            return NBT::matchTree($this->getNamedTag(), $item->getNamedTag());
        }

        return false;
    }
}
