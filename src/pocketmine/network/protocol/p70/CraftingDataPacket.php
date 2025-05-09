<?php

namespace pocketmine\network\protocol\p70;

use pocketmine\inventory\FurnaceRecipe;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\enchantment\EnchantmentList;
use pocketmine\utils\p70\BinaryStream;

use function chr;
use function count;
use function pack;
use function strlen;

class CraftingDataPacket extends DataPacket
{
    const NETWORK_ID = Info::CRAFTING_DATA_PACKET;

    const ENTRY_SHAPELESS = 0;
    const ENTRY_SHAPED = 1;
    const ENTRY_FURNACE = 2;
    const ENTRY_FURNACE_DATA = 3;
    const ENTRY_ENCHANT_LIST = 4;

    /** @var object[] */
    public $entries = [];
    public $cleanRecipes = false;

    private static function writeEntry($entry, BinaryStream $stream)
    {
        if ($entry instanceof ShapelessRecipe) {
            return self::writeShapelessRecipe($entry, $stream);
        } elseif ($entry instanceof ShapedRecipe) {
            return self::writeShapedRecipe($entry, $stream);
        } elseif ($entry instanceof FurnaceRecipe) {
            return self::writeFurnaceRecipe($entry, $stream);
        } elseif ($entry instanceof EnchantmentList) {
            return self::writeEnchantList($entry, $stream);
        }

        return -1;
    }

    private static function writeShapelessRecipe(ShapelessRecipe $recipe, BinaryStream $stream)
    {
        $stream->putInt($recipe->getIngredientCount());
        foreach ($recipe->getIngredientList() as $item) {
            $stream->putSlot($item);
        }

        $stream->putInt(1);
        $stream->putSlot($recipe->getResult());

        $stream->putUUID($recipe->getId());

        return CraftingDataPacket::ENTRY_SHAPELESS;
    }

    private static function writeShapedRecipe(ShapedRecipe $recipe, BinaryStream $stream)
    {
        $stream->putInt($recipe->getWidth());
        $stream->putInt($recipe->getHeight());

        for ($z = 0; $z < $recipe->getWidth(); ++$z) {
            for ($x = 0; $x < $recipe->getHeight(); ++$x) {
                $stream->putSlot($recipe->getIngredient($x, $z));
            }
        }

        $stream->putInt(1);
        $stream->putSlot($recipe->getResult());

        $stream->putUUID($recipe->getId());

        return CraftingDataPacket::ENTRY_SHAPED;
    }

    private static function writeFurnaceRecipe(FurnaceRecipe $recipe, BinaryStream $stream)
    {
        if ($recipe->getInput()->getDamage() !== 0) { //Data recipe
            $stream->putInt(($recipe->getInput()->getId() << 16) | ($recipe->getInput()->getDamage()));
            $stream->putSlot($recipe->getResult());

            return CraftingDataPacket::ENTRY_FURNACE_DATA;
        } else {
            $stream->putInt($recipe->getInput()->getId());
            $stream->putSlot($recipe->getResult());

            return CraftingDataPacket::ENTRY_FURNACE;
        }
    }

    private static function writeEnchantList(EnchantmentList $list, BinaryStream $stream)
    {

        $stream->putByte($list->getSize());
        for ($i = 0; $i < $list->getSize(); ++$i) {
            $entry = $list->getSlot($i);
            $stream->putInt($entry->getCost());
            $stream->putByte(count($entry->getEnchantments()));
            foreach ($entry->getEnchantments() as $enchantment) {
                $stream->putInt($enchantment->getId());
                $stream->putInt($enchantment->getLevel());
            }
            $stream->putString($entry->getRandomName());
        }

        return CraftingDataPacket::ENTRY_ENCHANT_LIST;
    }

    public function addShapelessRecipe(ShapelessRecipe $recipe)
    {
        $this->entries[] = $recipe;
    }

    public function addShapedRecipe(ShapedRecipe $recipe)
    {
        $this->entries[] = $recipe;
    }

    public function addFurnaceRecipe(FurnaceRecipe $recipe)
    {
        $this->entries[] = $recipe;
    }

    public function addEnchantList(EnchantmentList $list)
    {
        $this->entries[] = $list;
    }

    public function clean()
    {
        $this->entries = [];
        return parent::clean();
    }

    public function decode()
    {

    }

    public function encode()
    {
        $this->buffer = chr(self::NETWORK_ID);
        $this->offset = 0;
        ;
        $this->buffer .= pack("N", count($this->entries));

        $writer = new BinaryStream();
        foreach ($this->entries as $d) {
            $entryType = self::writeEntry($d, $writer);
            if ($entryType >= 0) {
                $this->buffer .= pack("N", $entryType);
                $this->buffer .= pack("N", strlen($writer->getBuffer()));
                $this->buffer .= $writer->getBuffer();
            } else {
                $this->buffer .= pack("N", -1);
                $this->buffer .= pack("N", 0);
            }

            $writer->reset();
        }

        $this->buffer .= chr($this->cleanRecipes ? 1 : 0);
    }
}
