<?php

namespace pocketmine\inventory\p70;

use pocketmine\block\Planks;
use pocketmine\block\Wood;
use pocketmine\block\Wood2;
use pocketmine\inventory\BigShapedRecipe;
use pocketmine\inventory\BrewingRecipe;
use pocketmine\inventory\FurnaceRecipe;
use pocketmine\inventory\Recipe;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Dye;
use pocketmine\item\Fish;
use pocketmine\item\Item;
use pocketmine\item\Potion;
use pocketmine\utils\UUID;

use function count;
use function min;
use function strlen;
use function substr_count;
use function usort;

class CraftingManager
{
    /** @var Recipe[] */
    public $recipes = [];

    /** @var Recipe[][] */
    protected $recipeLookup = [];

    /** @var FurnaceRecipe[] */
    public $furnaceRecipes = [];

    private static $RECIPE_COUNT = 0;

    public function __construct()
    {
        $this->registerFurnace();
        $this->registerDyes();
        $this->registerIngots();
        $this->registerTools();
        $this->registerWeapons();
        $this->registerArmor();
        $this->registerFood();
        $this->registerPlank();
        //$this->registerShapelessRecipeMap();
        $this->registerShapedRecipeMap();
        $this->registerBigShapedRecipeMap();
    }

    public function registerPlank()
    {
        $WOOD_CLASS = [
            Item::WOOD,
            Item::WOOD,
            Item::WOOD,
            Item::WOOD,
            Item::WOOD2,
            Item::WOOD2
        ];

        $WOOD = [
            Wood::OAK,
            Wood::SPRUCE,
            Wood::BIRCH,
            Wood::JUNGLE,
            Wood2::ACACIA,
            Wood2::DARK_OAK
        ];

        $PLANK = [
            Planks::OAK,
            Planks::SPRUCE,
            Planks::BIRCH,
            Planks::JUNGLE,
            Planks::ACACIA,
            Planks::DARK_OAK
        ];
        for ($i = 0;$i < count($WOOD);$i++) {
            $this->registerRecipe((new ShapedRecipe(
                Item::get(Item::WOODEN_PLANK, $PLANK[$i], 4),
                "X"
            ))->setIngredient("X", Item::get($WOOD_CLASS[$i], $WOOD[$i], 1)));
        }
    }

    public function registerShapelessRecipeMap()
    {
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::GLOWSTONE_BLOCK, 0, 1)))->addIngredient(Item::get(Item::GLOWSTONE_DUST, 0, 4)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::LIT_PUMPKIN, 0, 1)))->addIngredient(Item::get(Item::PUMPKIN, 0, 1))->addIngredient(Item::get(Item::TORCH, 0, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::CLAY_BLOCK, 0, 1)))->addIngredient(Item::get(Item::CLAY, 0, 4)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::SNOW_LAYER, 0, 6)))->addIngredient(Item::get(Item::SNOW_BLOCK, 0, 3)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 4, 9)))->addIngredient(Item::get(Item::LAPIS_BLOCK, 0, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::EMERALD, 0, 9)))->addIngredient(Item::get(Item::EMERALD_BLOCK, 0, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::COAL, 0, 9)))->addIngredient(Item::get(Item::COAL_BLOCK, 0, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::GOLD_NUGGET, 0, 9)))->addIngredient(Item::get(Item::GOLD_INGOT, 0, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::WOODEN_PLANK, Planks::OAK, 4)))->addIngredient(Item::get(Item::WOOD, Wood::OAK, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::WOODEN_PLANK, Planks::SPRUCE, 4)))->addIngredient(Item::get(Item::WOOD, Wood::SPRUCE, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::WOODEN_PLANK, Planks::BIRCH, 4)))->addIngredient(Item::get(Item::WOOD, Wood::BIRCH, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::WOODEN_PLANK, Planks::JUNGLE, 4)))->addIngredient(Item::get(Item::WOOD, Wood::JUNGLE, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::WOODEN_PLANK, Planks::ACACIA, 4)))->addIngredient(Item::get(Item::WOOD2, Wood2::ACACIA, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::WOODEN_PLANK, Planks::DARK_OAK, 4)))->addIngredient(Item::get(Item::WOOD2, Wood2::DARK_OAK, 1)));
    }

    public function registerShapedRecipeMap()
    {
        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::WORKBENCH, 0, 1),
            "XX",
            "XX"
        ))->setIngredient("X", Item::get(Item::WOODEN_PLANK, null)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::SNOW_BLOCK, 0, 1),
            "XX",
            "XX"
        ))->setIngredient("X", Item::get(Item::SNOWBALL, 0, 16)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::STICK, 0, 4),
            "X",
            "X"
        ))->setIngredient("X", Item::get(Item::WOODEN_PLANK, null)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::WOOL, 0, 1),
            "XX",
            "XX"
        ))->setIngredient("X", Item::get(Item::STRING, 0, 4)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::TORCH, 0, 4),
            "C",
            "S"
        ))->setIngredient("C", Item::get(Item::COAL, null))->setIngredient("S", Item::get(Item::STICK, null)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::REDSTONE_TORCH, 0, 1),
            "R",
            "S"
        ))->setIngredient("R", Item::get(Item::REDSTONE_DUST, null))->setIngredient("S", Item::get(Item::STICK, null)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::STONE_PRESSURE_PLATE, 0, 1),
            "SS"
        ))->setIngredient("S", Item::get(Item::STONE, 0, 2)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::WOODEN_PRESSURE_PLATE, 0, 1),
            "WW"
        ))->setIngredient("W", Item::get(Item::WOODEN_PLANK, null)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::LIGHT_WEIGHTED_PRESSURE_PLATE, 0, 1),
            "GG"
        ))->setIngredient("G", Item::get(Item::GOLD_INGOT, 0, 2)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::HEAVY_WEIGHTED_PRESSURE_PLATE, 0, 1),
            "II"
        ))->setIngredient("I", Item::get(Item::IRON_INGOT, 0, 2)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::SUGAR, 0, 1),
            "S"
        ))->setIngredient("S", Item::get(Item::SUGARCANE, 0, 1)));
    }

    public function registerBigShapedRecipeMap()
    {
        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::RAIL, 0, 16),
            "I I",
            "ISI",
            "I I"
        ))->setIngredient("I", Item::get(Item::IRON_INGOT, 0, 6))->setIngredient("S", Item::get(Item::STICK, 0, 1)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BOAT, 0, 1),
            "WSW",
            "WWW"
        ))->setIngredient("W", Item::get(Item::WOODEN_PLANK, 0, 5))->setIngredient("S", Item::get(Item::WOODEN_SHOVEL, 0, 1)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::POWERED_RAIL, 0, 6),
            "G G",
            "GSG",
            "GRG"
        ))->setIngredient("G", Item::get(Item::GOLD_INGOT, 0, 6))->setIngredient("S", Item::get(Item::STICK, 0, 1))->setIngredient("R", Item::get(Item::REDSTONE_DUST, 0, 1)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::ANVIL, 0, 1),
            "XXX",
            " I ",
            "III"
        ))->setIngredient("X", Item::get(Item::IRON_BLOCK, 0, 3))->setIngredient("I", Item::get(Item::IRON_INGOT, 0, 4)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::ENCHANTING_TABLE, 0, 1),
            " B ",
            "DID",
            "III"
        ))->setIngredient("B", Item::get(Item::BOOK, 0, 1))->setIngredient("D", Item::get(Item::DIAMOND, 0, 2))->setIngredient("I", Item::get(Item::OBSIDIAN, 0, 4)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::NETHER_REACTOR, 0, 1),
            "IDI",
            "IDI",
            "IDI"
        ))->setIngredient("D", Item::get(Item::DIAMOND, 0, 3))->setIngredient("I", Item::get(Item::IRON_INGOT, 0, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::REDSTONE_LAMP),
            " R ",
            "RGR",
            " R "
        ))->setIngredient("R", Item::get(Item::REDSTONE_DUST, 0, 4))->setIngredient("G", Item::get(Item::GLOWSTONE, 0, 1)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::BOOK, 0, 1),
            "PP ",
            "XP "
        ))->setIngredient("P", Item::get(Item::PAPER, null, 6))->setIngredient("X", Item::get(Item::LEATHER, 0, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BED, 0, 1),
            "WWW",
            "PPP"
        ))->setIngredient("W", Item::get(Item::WOOL, null, 3))->setIngredient("P", Item::get(Item::WOODEN_PLANK, null, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::CHEST, 0, 1),
            "PPP",
            "P P",
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, null, 8)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, 0, 3),
            "PSP",
            "PSP"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 2))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::OAK, 4)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, Planks::SPRUCE, 3),
            "PSP",
            "PSP"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 2))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::SPRUCE, 4)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, Planks::BIRCH, 3),
            "PSP",
            "PSP"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 2))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::BIRCH, 4)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, Planks::JUNGLE, 3),
            "PSP",
            "PSP"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 2))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::JUNGLE, 4)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, Planks::ACACIA, 3),
            "PSP",
            "PSP"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 2))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::ACACIA, 4)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, Planks::DARK_OAK, 3),
            "PSP",
            "PSP"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 2))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::DARK_OAK, 4)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE_GATE, 0, 1),
            "SPS",
            "SPS"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 4))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::OAK, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE_GATE_SPRUCE, 0, 1),
            "SPS",
            "SPS"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 4))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::SPRUCE, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE_GATE_BIRCH, 0, 1),
            "SPS",
            "SPS"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 4))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::BIRCH, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE_GATE_JUNGLE, 0, 1),
            "SPS",
            "SPS"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 4))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::JUNGLE, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE_GATE_DARK_OAK, 0, 1),
            "SPS",
            "SPS"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 4))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::DARK_OAK, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE_GATE_ACACIA, 0, 1),
            "SPS",
            "SPS"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 4))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::ACACIA, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FURNACE, 0, 1),
            "CCC",
            "C C",
            "CCC"
        ))->setIngredient("C", Item::get(Item::COBBLESTONE, 0, 8)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::GLASS_PANE, 0, 16),
            "GGG",
            "GGG"
        ))->setIngredient("G", Item::get(Item::GLASS, 0, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::LADDER, 0, 3),
            "S S",
            "SSS",
            "S S"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 7)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::TRAPDOOR, 0, 2),
            "PPP",
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, null, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::OAK_DOOR, 0, 3),
            "PP",
            "PP",
            "PP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::OAK, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::SPRUCE_DOOR, 0, 3),
            "PP",
            "PP",
            "PP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::SPRUCE, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BIRCH_DOOR, 0, 3),
            "PP",
            "PP",
            "PP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::BIRCH, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::JUNGLE_DOOR, 0, 3),
            "PP",
            "PP",
            "PP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::JUNGLE, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::ACACIA_DOOR, 0, 3),
            "PP",
            "PP",
            "PP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::ACACIA, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::DARK_OAK_DOOR, 0, 3),
            "PP",
            "PP",
            "PP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::DARK_OAK, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::WOODEN_STAIRS, 0, 4),
            "P  ",
            "PP ",
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::OAK, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::WOOD_SLAB, Planks::OAK, 6),
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::OAK, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::WOOD_SLAB, Planks::SPRUCE, 6),
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::SPRUCE, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::WOOD_SLAB, Planks::BIRCH, 6),
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::BIRCH, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::WOOD_SLAB, Planks::JUNGLE, 6),
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::JUNGLE, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::WOOD_SLAB, Planks::ACACIA, 6),
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::ACACIA, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::WOOD_SLAB, Planks::DARK_OAK, 6),
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::DARK_OAK, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::SPRUCE_WOOD_STAIRS, 0, 4),
            "P  ",
            "PP ",
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::SPRUCE, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BIRCH_WOOD_STAIRS, 0, 4),
            "P  ",
            "PP ",
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::BIRCH, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::JUNGLE_WOOD_STAIRS, 0, 4),
            "P  ",
            "PP ",
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::JUNGLE, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::ACACIA_WOOD_STAIRS, 0, 4),
            "P  ",
            "PP ",
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::ACACIA, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::DARK_OAK_WOOD_STAIRS, 0, 4),
            "P  ",
            "PP ",
            "PPP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, Planks::DARK_OAK, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BUCKET, 0, 1),
            "I I",
            " I "
        ))->setIngredient("I", Item::get(Item::IRON_INGOT, 0, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::CLOCK, 0, 1),
            " G ",
            "GRG",
            " G "
        ))->setIngredient("G", Item::get(Item::GOLD_INGOT, 0, 4))->setIngredient("R", Item::get(Item::REDSTONE_DUST, 0, 1)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::COMPASS, 0, 1),
            " I ",
            "IRI",
            " I "
        ))->setIngredient("I", Item::get(Item::IRON_INGOT, 0, 4))->setIngredient("R", Item::get(Item::REDSTONE_DUST, 0, 1)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::TNT, 0, 1),
            "GSG",
            "SGS",
            "GSG"
        ))->setIngredient("G", Item::get(Item::GUNPOWDER, 0, 5))->setIngredient("S", Item::get(Item::SAND, null, 4)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BOWL, 0, 4),
            "P P",
            " P "
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANKS, null, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::MINECART, 0, 1),
            "I I",
            "III"
        ))->setIngredient("I", Item::get(Item::IRON_INGOT, 0, 5)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BOOKSHELF, 0, 1),
            "PBP",
            "PBP",
            "PBP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANK, null, 6))->setIngredient("B", Item::get(Item::BOOK, 0, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::PAINTING, 0, 1),
            "SSS",
            "SWS",
            "SSS"
        ))->setIngredient("S", Item::get(Item::STICK, 0, 8))->setIngredient("W", Item::get(Item::WOOL, null, 1)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::PAPER, 0, 3),
            "SSS"
        ))->setIngredient("S", Item::get(Item::SUGARCANE, 0, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::SIGN, 0, 3),
            "PPP",
            "PPP",
            " S "
        ))->setIngredient("S", Item::get(Item::STICK, 0, 1))->setIngredient("P", Item::get(Item::WOODEN_PLANKS, null, 6))); //TODO: check if it gives one sign or three


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::IRON_BARS, 0, 16),
            "III",
            "III"
        ))->setIngredient("I", Item::get(Item::IRON_INGOT, 0, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BREWING_STAND, 0, 1),
            " B ",
            "CCC"
        ))->setIngredient("B", Item::get(Item::BLAZE_ROD, 0, 1))->setIngredient("C", Item::get(Item::COBBLESTONE, 0, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::LAPIS_BLOCK, 0, 1),
            "DDD",
            "DDD",
            "DDD"
        ))->setIngredient("D", Item::get(Item::DYE, Dye::LAPIS_LAZULI, 9)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::GOLD_INGOT, 0, 1),
            "GGG",
            "GGG",
            "GGG"
        ))->setIngredient("G", Item::get(Item::GOLD_NUGGET, 0, 9)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::COBBLESTONE_STAIRS, 0, 4),
            "S  ",
            "SS ",
            "SSS"
        ))->setIngredient("S", Item::get(Item::COBBLESTONE, 0, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::SANDSTONE_STAIRS, 0, 4),
            "S  ",
            "SS ",
            "SSS"
        ))->setIngredient("S", Item::get(Item::SANDSTONE, 0, 6)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BREAD, 0, 1),
            "WWW"
        ))->setIngredient("W", Item::get(Item::WHEAT, 0, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, 0, 3),
            "PSP",
            "PSP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANKS, 0, 4))->setIngredient("S", Item::get(Item::STICK, 0, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, 1, 3),
            "PSP",
            "PSP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANKS, 1, 4))->setIngredient("S", Item::get(Item::STICK, 0, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, 2, 3),
            "PSP",
            "PSP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANKS, 2, 4))->setIngredient("S", Item::get(Item::STICK, 0, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, 3, 3),
            "PSP",
            "PSP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANKS, 3, 4))->setIngredient("S", Item::get(Item::STICK, 0, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, 4, 3),
            "PSP",
            "PSP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANKS, 4, 4))->setIngredient("S", Item::get(Item::STICK, 0, 2)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::FENCE, 5, 3),
            "PSP",
            "PSP"
        ))->setIngredient("P", Item::get(Item::WOODEN_PLANKS, 5, 4))->setIngredient("S", Item::get(Item::STICK, 0, 2)));
    }

    protected function registerFurnace()
    {
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::STONE, 0, 1), Item::get(Item::COBBLESTONE, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::STONE_BRICK, 2, 1), Item::get(Item::STONE_BRICK, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::GLASS, 0, 1), Item::get(Item::SAND, null, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::COAL, 1, 1), Item::get(Item::TRUNK, null, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::GOLD_INGOT, 0, 1), Item::get(Item::GOLD_ORE, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::IRON_INGOT, 0, 1), Item::get(Item::IRON_ORE, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::EMERALD, 0, 1), Item::get(Item::EMERALD_ORE, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::DIAMOND, 0, 1), Item::get(Item::DIAMOND_ORE, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::NETHER_BRICK, 0, 1), Item::get(Item::NETHERRACK, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::COOKED_PORKCHOP, 0, 1), Item::get(Item::RAW_PORKCHOP, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::BRICK, 0, 1), Item::get(Item::CLAY, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::COOKED_FISH, 0, 1), Item::get(Item::RAW_FISH, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::COOKED_SALMON, 0, 1), Item::get(Item::RAW_SALMON, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::DYE, 2, 1), Item::get(Item::CACTUS, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::DYE, 1, 1), Item::get(Item::RED_MUSHROOM, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::STEAK, 0, 1), Item::get(Item::RAW_BEEF, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::COOKED_CHICKEN, 0, 1), Item::get(Item::RAW_CHICKEN, 0, 1)));
        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::BAKED_POTATO, 0, 1), Item::get(Item::POTATO, 0, 1)));

        $this->registerRecipe(new FurnaceRecipe(Item::get(Item::HARDENED_CLAY, 0, 1), Item::get(Item::CLAY_BLOCK, 0, 1)));
    }

    protected function registerBrewingStand()
    {
        //base
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::AWKWARD, 1), Item::get(Item::NETHER_WART, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::MUNDANE_EXTENDED, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::MUNDANE, 1), Item::get(Item::GHAST_TEAR, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::MUNDANE, 1), Item::get(Item::GLISTERING_MELON, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::MUNDANE, 1), Item::get(Item::BLAZE_POWDER, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::MUNDANE, 1), Item::get(Item::MAGMA_CREAM, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::MUNDANE, 1), Item::get(Item::SUGAR, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::MUNDANE, 1), Item::get(Item::SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::MUNDANE, 1), Item::get(Item::RABBIT_FOOT, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::THICK, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::WATER_BOTTLE, 1)));
        //secondary positive
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::HEALING, 1), Item::get(Item::GLISTERING_MELON, 0, 1), Item::get(Item::POTION, Potion::AWKWARD, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::FIRE_RESISTANCE, 1), Item::get(Item::MAGMA_CREAM, 0, 1), Item::get(Item::POTION, Potion::AWKWARD, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::REGENERATION, 1), Item::get(Item::GHAST_TEAR, 0, 1), Item::get(Item::POTION, Potion::AWKWARD, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::STRENGTH, 1), Item::get(Item::BLAZE_POWDER, 0, 1), Item::get(Item::POTION, Potion::AWKWARD, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SPEED, 1), Item::get(Item::SUGAR, 0, 1), Item::get(Item::POTION, Potion::AWKWARD, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::NIGHT_VISION, 1), Item::get(Item::GOLDEN_CARROT, 0, 1), Item::get(Item::POTION, Potion::AWKWARD, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WATER_BREATHING, 1), Item::get(Item::RAW_FISH, Fish::PUFFERFISH, 1), Item::get(Item::POTION, Potion::AWKWARD, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::LEAPING, 1), Item::get(Item::RABBIT_FOOT, 0, 1), Item::get(Item::POTION, Potion::AWKWARD, 1)));
        //secondary negative
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::POISON, 1), Item::get(Item::SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::AWKWARD, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::AWKWARD, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::THICK, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::MUNDANE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS_T, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::MUNDANE_EXTENDED, 1)));
        //tertiary positive
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::FIRE_RESISTANCE_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::FIRE_RESISTANCE, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::HEALING_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::HEALING, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::REGENERATION_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::REGENERATION, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::REGENERATION_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::REGENERATION_TWO, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::REGENERATION_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::REGENERATION, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::REGENERATION_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::REGENERATION_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::STRENGTH_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::STRENGTH, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::STRENGTH_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::STRENGTH_TWO, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::STRENGTH_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::STRENGTH, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::STRENGTH_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::STRENGTH_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SPEED_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::SPEED, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SPEED_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::SPEED_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::NIGHT_VISION_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::NIGHT_VISION, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::INVISIBILITY, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::NIGHT_VISION, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::INVISIBILITY_T, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::NIGHT_VISION_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::INVISIBILITY_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::INVISIBILITY, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WATER_BREATHING_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::WATER_BREATHING, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::LEAPING_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::LEAPING, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::LEAPING_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::LEAPING_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::LEAPING_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::LEAPING, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::LEAPING_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::LEAPING_TWO, 1)));
        //tertiary negative
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::HARMING, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::HEALING, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::HARMING, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::POISON, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::HARMING, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::POISON_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::HARMING_TWO, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::HEALING_TWO, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::HARMING_TWO, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::POISON_TWO, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::HARMING_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::HARMING, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::POISON_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::POISON, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::POISON_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::POISON_TWO, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::POISON_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::POISON, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::POISON_TWO, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::POISON_T, 1)));
        //$this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SLOWNESS, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::FIRE_RESISTANCE, 1))); // removed?
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SLOWNESS, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::SPEED, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SLOWNESS, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::LEAPING, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SLOWNESS_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::SLOWNESS, 1)));
        //$this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SLOWNESS_T, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::FIRE_RESISTANCE_T, 1))); // removed?
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SLOWNESS_T, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::SPEED_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SLOWNESS_T, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::LEAPING_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::STRENGTH, 1)));
        //$this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::REGENERATION, 1))); // removed?
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::STRENGTH, 1)));
        //$this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS_T, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::REGENERATION_T, 1))); // removed?
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS_T, 1), Item::get(Item::FERMENTED_SPIDER_EYE, 0, 1), Item::get(Item::POTION, Potion::STRENGTH_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS_T, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::WEAKNESS, 1)));
        //reverted
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::FIRE_RESISTANCE, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::FIRE_RESISTANCE_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::NIGHT_VISION, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::NIGHT_VISION_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::SLOWNESS, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::SLOWNESS_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WEAKNESS, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::WEAKNESS_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::WATER_BREATHING, 1), Item::get(Item::GLOWSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::WATER_BREATHING_T, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::HEALING, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::HEALING_TWO, 1)));
        $this->registerRecipe(new BrewingRecipe(Item::get(Item::POTION, Potion::HARMING, 1), Item::get(Item::REDSTONE_DUST, 0, 1), Item::get(Item::POTION, Potion::HARMING_TWO, 1)));



    }

    private function sortAndAddRecipesArray($recipes) // removed a &
    {// Sort the recipes based on the result item name with the bubblesort algoritm.
        for ($i = 0; $i < count($recipes); ++$i) {
            $current = $recipes[$i];
            $result = $current->getResult();
            for ($j = count($recipes) - 1; $j > $i; --$j) {
                if ($this->sort($result, $recipes[$j]->getResult()) > 0) {
                    $swap = $current;
                    $current = $recipes[$j];
                    $recipes[$j] = $swap;
                    $result = $current->getResult();
                }
            }
            $this->registerRecipe($current);
        }
    }

    private function createOneIngedientRecipe($recipeshape, $resultitem, $resultitemmeta, $resultitemamound, $ingedienttype, $ingredientmeta, $ingredientname, $inventoryType = "")
    {
        $ingredientamount = 0;
        $height = 0;
        // count how many of the ingredient are in the recipe and check height for big or small recipe.
        foreach ($recipeshape as $line) {
            $height += 1;
            $width = strlen($line);
            $ingredientamount += substr_count($line, $ingredientname);
        }
        $recipe = null;
        if ($height < 3) {
            // Process small recipe
            $fullClassName = "pocketmine\\inventory\\" . $inventoryType . "ShapedRecipe";// $ShapeClass."ShapedRecipe";
            $recipe = ((new $fullClassName(
                Item::get($resultitem, $resultitemmeta, $resultitemamound),
                ...$recipeshape
            ))->setIngredient($ingredientname, Item::get($ingedienttype, $ingredientmeta, $ingredientamount)));
        } else {
            // Process big recipe
            $fullClassName = "pocketmine\\inventory\\" . $inventoryType . "BigShapedRecipe";
            $recipe = ((new $fullClassName(
                Item::get($resultitem, $resultitemmeta, $resultitemamound),
                ...$recipeshape
            ))->setIngredient($ingredientname, Item::get($ingedienttype, $ingredientmeta, $ingredientamount)));
        }
        return $recipe;
    }

    protected function registerFood()
    {

        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::MELON_SEEDS, 0, 1)))->addIngredient(Item::get(Item::MELON_SLICE, 0, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::PUMPKIN_SEEDS, 0, 4)))->addIngredient(Item::get(Item::PUMPKIN, 0, 1)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::PUMPKIN_PIE, 0, 1),
            "PS",
            " E"
        ))->setIngredient("P", Item::get(Item::PUMPKIN, 0, 1))->setIngredient("E", Item::get(Item::EGG, 0, 1))->setIngredient("S", Item::get(Item::SUGAR, 0, 1)));


        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::MUSHROOM_STEW, 0, 1),
            "RM",
            " B"
        ))->setIngredient("B", Item::get(Item::BOWL, 0, 1))->setIngredient("M", Item::get(Item::BROWN_MUSHROOM, 0, 1))->setIngredient("R", Item::get(Item::RED_MUSHROOM, 0, 1)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::MELON_BLOCK, 0, 1),
            "MMM",
            "MMM",
            "MMM"
        ))->setIngredient("M", Item::get(Item::MELON_SLICE, 0, 9)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BEETROOT_SOUP, 0, 1),
            "XXX",
            "XXX",
            " B "
        ))->setIngredient("X", Item::get(Item::BEETROOT, 0, 46))->setIngredient("B", Item::get(Item::BOWL, 0, 1)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BREAD, 0, 1),
            "WWW"
        ))->setIngredient("W", Item::get(Item::WHEAT, 0, 3)));


        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::CAKE_BLOCK, 0, 1),
            "MMM",
            "SES",
            "WWW"
        ))->setIngredient("W", Item::get(Item::WHEAT, 0, 3))->setIngredient("E", Item::get(Item::EGG, 0, 1))->setIngredient("S", Item::get(Item::SUGAR, 0, 2))->setIngredient("M", Item::get(Item::BUCKET, 1, 3)));
    }

    protected function registerArmor()
    {
        $types = [
            [Item::LEATHER, Item::FIRE, Item::IRON_INGOT, Item::DIAMOND, Item::GOLD_INGOT],
            [Item::LEATHER_CAP, Item::CHAIN_HELMET, Item::IRON_HELMET, Item::DIAMOND_HELMET, Item::GOLD_HELMET],
            [Item::LEATHER_TUNIC, Item::CHAIN_CHESTPLATE, Item::IRON_CHESTPLATE, Item::DIAMOND_CHESTPLATE, Item::GOLD_CHESTPLATE],
            [Item::LEATHER_PANTS, Item::CHAIN_LEGGINGS, Item::IRON_LEGGINGS, Item::DIAMOND_LEGGINGS, Item::GOLD_LEGGINGS],
            [Item::LEATHER_BOOTS, Item::CHAIN_BOOTS, Item::IRON_BOOTS, Item::DIAMOND_BOOTS, Item::GOLD_BOOTS],
        ];

        $shapes = [
            [
                "XXX",
                "X X",
                "   "
            ],
            [
                "X X",
                "XXX",
                "XXX"
            ],
            [
                "XXX",
                "X X",
                "X X"
            ],
            [

                "X X",
                "X X"
            ]
        ];

        for ($i = 1; $i < 5; ++$i) {
            foreach ($types[$i] as $j => $type) {
                $this->registerRecipe((new BigShapedRecipe(Item::get($type, 0, 1), ...$shapes[$i - 1]))->setIngredient("X", Item::get($types[0][$j], 0, 1)));
            }
        }
    }

    protected function registerWeapons()
    {
        $types = [
            [Item::WOODEN_PLANK, Item::COBBLESTONE, Item::IRON_INGOT, Item::DIAMOND, Item::GOLD_INGOT],
            [Item::WOODEN_SWORD, Item::STONE_SWORD, Item::IRON_SWORD, Item::DIAMOND_SWORD, Item::GOLD_SWORD],
        ];


        for ($i = 1; $i < 2; ++$i) {
            foreach ($types[$i] as $j => $type) {
                $this->registerRecipe((new BigShapedRecipe(
                    Item::get($type, 0, 1),
                    " X ",
                    " X ",
                    " I "
                ))->setIngredient("X", Item::get($types[0][$j], null))->setIngredient("I", Item::get(Item::STICK)));
            }
        }

        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::ARROW, 0, 1),
            " F ",
            " S ",
            " P "
        ))->setIngredient("S", Item::get(Item::STICK))->setIngredient("F", Item::get(Item::FLINT))->setIngredient("P", Item::get(Item::FEATHER)));

        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::BOW, 0, 1),
            " X~",
            "X ~",
            " X~"
        ))->setIngredient("~", Item::get(Item::STRING))->setIngredient("X", Item::get(Item::STICK)));
    }

    protected function registerTools()
    {
        $types = [
            [Item::WOODEN_PLANK, Item::COBBLESTONE, Item::IRON_INGOT, Item::DIAMOND, Item::GOLD_INGOT],
            [Item::WOODEN_PICKAXE, Item::STONE_PICKAXE, Item::IRON_PICKAXE, Item::DIAMOND_PICKAXE, Item::GOLD_PICKAXE],
            [Item::WOODEN_SHOVEL, Item::STONE_SHOVEL, Item::IRON_SHOVEL, Item::DIAMOND_SHOVEL, Item::GOLD_SHOVEL],
            [Item::WOODEN_AXE, Item::STONE_AXE, Item::IRON_AXE, Item::DIAMOND_AXE, Item::GOLD_AXE],
            [Item::WOODEN_HOE, Item::STONE_HOE, Item::IRON_HOE, Item::DIAMOND_HOE, Item::GOLD_HOE],
        ];
        $shapes = [
            [
                "XXX",
                " I ",
                " I "
            ],
            [
                " X ",
                " I ",
                " I "
            ],
            [
                "XX ",
                "XI ",
                " I "
            ],
            [
                "XX ",
                " I ",
                " I "
            ]
        ];

        for ($i = 1; $i < 5; ++$i) {
            foreach ($types[$i] as $j => $type) {
                $this->registerRecipe((new BigShapedRecipe(Item::get($type, 0, 1), ...$shapes[$i - 1]))->setIngredient("X", Item::get($types[0][$j], null))->setIngredient("I", Item::get(Item::STICK)));
            }
        }

        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::FLINT_AND_STEEL, 0, 1),
            " S",
            "F "
        ))->setIngredient("F", Item::get(Item::FLINT))->setIngredient("S", Item::get(Item::IRON_INGOT)));

        $this->registerRecipe((new ShapedRecipe(
            Item::get(Item::SHEARS, 0, 1),
            " X",
            "X "
        ))->setIngredient("X", Item::get(Item::IRON_INGOT)));
    }

    protected function registerDyes()
    {
        for ($i = 0; $i < 16; ++$i) {
            $this->registerRecipe((new ShapelessRecipe(Item::get(Item::WOOL, 15 - $i, 1)))->addIngredient(Item::get(Item::DYE, $i, 1))->addIngredient(Item::get(Item::WOOL, 0, 1)));
            $this->registerRecipe((new ShapelessRecipe(Item::get(Item::STAINED_CLAY, 15 - $i, 8)))->addIngredient(Item::get(Item::DYE, $i, 1))->addIngredient(Item::get(Item::HARDENED_CLAY, 0, 8)));
            //TODO: add glass things?
            $this->registerRecipe((new ShapelessRecipe(Item::get(Item::WOOL, 15 - $i, 1)))->addIngredient(Item::get(Item::DYE, $i, 1))->addIngredient(Item::get(Item::WOOL, 0, 1)));
            $this->registerRecipe((new ShapelessRecipe(Item::get(Item::WOOL, 15 - $i, 1)))->addIngredient(Item::get(Item::DYE, $i, 1))->addIngredient(Item::get(Item::WOOL, 0, 1)));
            $this->registerRecipe((new ShapelessRecipe(Item::get(Item::WOOL, 15 - $i, 1)))->addIngredient(Item::get(Item::DYE, $i, 1))->addIngredient(Item::get(Item::WOOL, 0, 1)));

            $this->registerRecipe((new ShapelessRecipe(Item::get(Item::CARPET, $i, 3)))->addIngredient(Item::get(Item::WOOL, $i, 2)));
        }

        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 11, 2)))->addIngredient(Item::get(Item::DANDELION, 0, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 15, 3)))->addIngredient(Item::get(Item::BONE, 0, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 3, 2)))->addIngredient(Item::get(Item::DYE, 14, 1))->addIngredient(Item::get(Item::DYE, 0, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 3, 3)))->addIngredient(Item::get(Item::DYE, 1, 1))->addIngredient(Item::get(Item::DYE, 0, 1))->addIngredient(Item::get(Item::DYE, 11, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 9, 2)))->addIngredient(Item::get(Item::DYE, 15, 1))->addIngredient(Item::get(Item::DYE, 1, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 14, 2)))->addIngredient(Item::get(Item::DYE, 11, 1))->addIngredient(Item::get(Item::DYE, 1, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 10, 2)))->addIngredient(Item::get(Item::DYE, 2, 1))->addIngredient(Item::get(Item::DYE, 15, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 12, 2)))->addIngredient(Item::get(Item::DYE, 4, 1))->addIngredient(Item::get(Item::DYE, 15, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 6, 2)))->addIngredient(Item::get(Item::DYE, 4, 1))->addIngredient(Item::get(Item::DYE, 2, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 5, 2)))->addIngredient(Item::get(Item::DYE, 4, 1))->addIngredient(Item::get(Item::DYE, 1, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 13, 3)))->addIngredient(Item::get(Item::DYE, 4, 1))->addIngredient(Item::get(Item::DYE, 1, 1))->addIngredient(Item::get(Item::DYE, 15, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 1, 1)))->addIngredient(Item::get(Item::BEETROOT, 0, 1)));

        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 13, 4)))->addIngredient(Item::get(Item::DYE, 15, 1))->addIngredient(Item::get(Item::DYE, 1, 2))->addIngredient(Item::get(Item::DYE, 4, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 13, 2)))->addIngredient(Item::get(Item::DYE, 5, 1))->addIngredient(Item::get(Item::DYE, 9, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 8, 2)))->addIngredient(Item::get(Item::DYE, 0, 1))->addIngredient(Item::get(Item::DYE, 15, 1)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 7, 3)))->addIngredient(Item::get(Item::DYE, 0, 1))->addIngredient(Item::get(Item::DYE, 15, 2)));
        $this->registerRecipe((new ShapelessRecipe(Item::get(Item::DYE, 7, 2)))->addIngredient(Item::get(Item::DYE, 0, 1))->addIngredient(Item::get(Item::DYE, 8, 1)));

    }

    protected function registerIngots()
    {
        $ingots = [
            Item::GOLD_BLOCK     => Item::GOLD_INGOT,
            Item::IRON_BLOCK     => Item::IRON_INGOT,
            Item::DIAMOND_BLOCK  => Item::DIAMOND,
            Item::EMERALD_BLOCK  => Item::EMERALD,
            Item::REDSTONE_BLOCK => Item::REDSTONE_DUST,
            Item::COAL_BLOCK     => Item::COAL,
            Item::HAY_BALE       => Item::WHEAT,
            //Item::GOLD_INGOT => Item::GOLD_NUGGET
        ];
        foreach ($ingots as $block => $ingot) {
            $this->registerRecipe((new BigShapedRecipe(
                Item::get($ingot, 0, 9),
                "   ",
                " D ",
                "   "
            ))->setIngredient("D", Item::get($block, 0, 1)));
            $this->registerRecipe((new BigShapedRecipe(
                Item::get($block, 0, 1),
                "GGG",
                "GGG",
                "GGG"
            ))->setIngredient("G", Item::get($ingot, 0, 9)));
        }
        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::LAPIS_BLOCK, 0, 1),
            "GGG",
            "GGG",
            "GGG"
        ))->setIngredient("G", Item::get(Item::DYE, 4, 9)));
        $this->registerRecipe((new BigShapedRecipe(
            Item::get(Item::DYE, 4, 9),
            "   ",
            " L ",
            "   "
        ))->setIngredient("L", Item::get(Item::LAPIS_BLOCK, 0, 1)));
    }

    public function sort(Item $i1, Item $i2)
    {
        if ($i1->getId() > $i2->getId()) {
            return 1;
        } elseif ($i1->getId() < $i2->getId()) {
            return -1;
        } elseif ($i1->getDamage() > $i2->getDamage()) {
            return 1;
        } elseif ($i1->getDamage() < $i2->getDamage()) {
            return -1;
        } elseif ($i1->getCount() > $i2->getCount()) {
            return 1;
        } elseif ($i1->getCount() < $i2->getCount()) {
            return -1;
        } else {
            return 0;
        }
    }

    /**
     * @param UUID $id
     * @return Recipe
     */
    public function getRecipe(UUID $id)
    {
        $index = $id->toBinary();
        return $this->recipes[$index] ?? null;
    }

    /**
     * @return Recipe[]
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * @return FurnaceRecipe[]
     */
    public function getFurnaceRecipes()
    {
        return $this->furnaceRecipes;
    }

    /**
     * @return FurnaceRecipe[]
     */
    public function getBrewingRecipes()
    {
        return $this->brewingRecipes;
    }

    /**
     * @param Item $input
     *
     * @return FurnaceRecipe
     */
    public function matchFurnaceRecipe(Item $input)
    {
        if (isset($this->furnaceRecipes[$input->getId() . ":" . $input->getDamage()])) {
            return $this->furnaceRecipes[$input->getId() . ":" . $input->getDamage()];
        } elseif (isset($this->furnaceRecipes[$input->getId() . ":?"])) {
            return $this->furnaceRecipes[$input->getId() . ":?"];
        }

        return null;
    }

    /**
     * @param Item $input
     *
     * @return BrewingRecipe
     */
    public function matchBrewingRecipe(Item $input)
    {
        if (isset($this->BrewingRecipes[$input->getId() . ":" . $input->getDamage()])) {
            return $this->BrewingRecipes[$input->getId() . ":" . $input->getDamage()];
        } elseif (isset($this->BrewingRecipes[$input->getId() . ":?"])) {
            return $this->BrewingRecipes[$input->getId() . ":?"];
        }
        return null;
    }

    /**
     * @param ShapedRecipe $recipe
     */
    public function registerShapedRecipe(ShapedRecipe $recipe)
    {
        $result = $recipe->getResult();
        $this->recipes[$recipe->getId()->toBinary()] = $recipe;
        $ingredients = $recipe->getIngredientMap();
        $hash = "";
        foreach ($ingredients as $v) {
            foreach ($v as $item) {
                if ($item !== null) {
                    /** @var Item $item */
                    $hash .= $item->getId() . ":" . ($item->getDamage() === null ? "?" : $item->getDamage()) . "x" . $item->getCount() . ",";
                }
            }

            $hash .= ";";
        }

        $this->recipeLookup[$result->getId() . ":" . $result->getDamage()][$hash] = $recipe;
    }

    /**
     * @param ShapelessRecipe $recipe
     */
    public function registerShapelessRecipe(ShapelessRecipe $recipe)
    {
        $result = $recipe->getResult();
        $this->recipes[$recipe->getId()->toBinary()] = $recipe;
        $hash = "";
        $ingredients = $recipe->getIngredientList();
        usort($ingredients, [$this, "sort"]);
        foreach ($ingredients as $item) {
            $hash .= $item->getId() . ":" . ($item->getDamage() === null ? "?" : $item->getDamage()) . "x" . $item->getCount() . ",";
        }
        $this->recipeLookup[$result->getId() . ":" . $result->getDamage()][$hash] = $recipe;
    }

    /**
     * @param FurnaceRecipe $recipe
     */
    public function registerFurnaceRecipe(FurnaceRecipe $recipe)
    {
        $input = $recipe->getInput();
        $this->furnaceRecipes[$input->getId() . ":" . ($input->getDamage() === null ? "?" : $input->getDamage())] = $recipe;
    }

    /**
     * @param BrewingRecipe $recipe
     */
    public function registerBrewingRecipe(BrewingRecipe $recipe)
    {
        $input = $recipe->getInput();
        $this->brewingRecipes[$input->getId() . ":" . ($input->getDamage() === null ? "?" : $input->getDamage())] = $recipe;
    }

    /**
     * @param ShapelessRecipe $recipe
     * @return bool
     */
    public function matchRecipe(ShapelessRecipe $recipe)
    {
        if (!isset($this->recipeLookup[$idx = $recipe->getResult()->getId() . ":" . $recipe->getResult()->getDamage()])) {
            return false;
        }

        $hash = "";
        $ingredients = $recipe->getIngredientList();
        usort($ingredients, [$this, "sort"]);
        foreach ($ingredients as $item) {
            $hash .= $item->getId() . ":" . ($item->getDamage() === null ? "?" : $item->getDamage()) . "x" . $item->getCount() . ",";
        }

        if (isset($this->recipeLookup[$idx][$hash])) {
            return true;
        }

        $hasRecipe = null;
        foreach ($this->recipeLookup[$idx] as $recipe) {
            if ($recipe instanceof ShapelessRecipe) {
                if ($recipe->getIngredientCount() !== count($ingredients)) {
                    continue;
                }
                $checkInput = $recipe->getIngredientList();
                foreach ($ingredients as $item) {
                    $amount = $item->getCount();
                    foreach ($checkInput as $k => $checkItem) {
                        if ($checkItem->equals($item, $checkItem->getDamage() === null ? false : true, $checkItem->getCompoundTag() === null ? false : true)) {
                            $remove = min($checkItem->getCount(), $amount);
                            $checkItem->setCount($checkItem->getCount() - $remove);
                            if ($checkItem->getCount() === 0) {
                                unset($checkInput[$k]);
                            }
                            $amount -= $remove;
                            if ($amount === 0) {
                                break;
                            }
                        }
                    }
                }

                if (count($checkInput) === 0) {
                    $hasRecipe = $recipe;
                    break;
                }
            }
            if ($hasRecipe instanceof Recipe) {
                break;
            }
        }

        return $hasRecipe !== null;

    }

    /**
     * @param Recipe $recipe
     */
    public function registerRecipe(Recipe $recipe)
    {
        $recipe->setId(UUID::fromData(++self::$RECIPE_COUNT, $recipe->getResult()->getId(), $recipe->getResult()->getDamage(), $recipe->getResult()->getCount(), $recipe->getResult()->getCompoundTag()));

        if ($recipe instanceof ShapedRecipe) {
            $this->registerShapedRecipe($recipe);
        } elseif ($recipe instanceof ShapelessRecipe) {
            $this->registerShapelessRecipe($recipe);
        } elseif ($recipe instanceof FurnaceRecipe) {
            $this->registerFurnaceRecipe($recipe);
        } elseif ($recipe instanceof BrewingRecipe) {
            $this->registerBrewingRecipe($recipe);
        }
    }
}
