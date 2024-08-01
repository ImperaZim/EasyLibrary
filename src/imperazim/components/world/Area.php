<?php

declare(strict_types = 1);

namespace imperazim\components\world;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\world\Position;

/**
* Class Area
* @package imperazim\components\world
*/
final class Area {

  /**
  * Get all blocks within the area defined by two positions.
  * @param Position $pos1
  * @param Position $pos2
  * @return array
  * @throws \InvalidArgumentException if the positions are not in the same world.
  */
  public static function getAreaBlocks(Position $pos1, Position $pos2): array {
    self::validatePositions($pos1, $pos2);

    $world = $pos1->getWorld();
    $blocks = [];

    $minX = min($pos1->getX(), $pos2->getX());
    $maxX = max($pos1->getX(), $pos2->getX());
    $minY = min($pos1->getY(), $pos2->getY());
    $maxY = max($pos1->getY(), $pos2->getY());
    $minZ = min($pos1->getZ(), $pos2->getZ());
    $maxZ = max($pos1->getZ(), $pos2->getZ());

    for ($x = $minX; $x <= $maxX; $x++) {
      for ($y = $minY; $y <= $maxY; $y++) {
        for ($z = $minZ; $z <= $maxZ; $z++) {
          $blocks[] = $world->getBlockAt($x, $y, $z);
        }
      }
    }

    return $blocks;
  }

  /**
  * Set blocks within the area defined by two positions.
  * @param Position $pos1
  * @param Position $pos2
  * @param Block|Block[] $blocks
  * @throws \InvalidArgumentException if the positions are not in the same world or if blocks are not valid.
  */
  public static function setAreaBlocks(Position $pos1, Position $pos2, $blocks): void {
    self::validatePositions($pos1, $pos2);
    self::validateBlocks($blocks);

    $world = $pos1->getWorld();

    $minX = min($pos1->getX(), $pos2->getX());
    $maxX = max($pos1->getX(), $pos2->getX());
    $minY = min($pos1->getY(), $pos2->getY());
    $maxY = max($pos1->getY(), $pos2->getY());
    $minZ = min($pos1->getZ(), $pos2->getZ());
    $maxZ = max($pos1->getZ(), $pos2->getZ());

    if (is_array($blocks)) {
      $blockCount = count($blocks);
      $blockIndex = 0;

      for ($x = $minX; $x <= $maxX; $x++) {
        for ($y = $minY; $y <= $maxY; $y++) {
          for ($z = $minZ; $z <= $maxZ; $z++) {
            $world->setBlockAt($x, $y, $z, $blocks[$blockIndex]);
            $blockIndex = ($blockIndex + 1) % $blockCount;
          }
        }
      }
    } else {
      for ($x = $minX; $x <= $maxX; $x++) {
        for ($y = $minY; $y <= $maxY; $y++) {
          for ($z = $minZ; $z <= $maxZ; $z++) {
            $world->setBlockAt($x, $y, $z, $blocks);
          }
        }
      }
    }
  }

  /**
  * Fill the entire area defined by two positions with a specific block.
  * @param Position $pos1
  * @param Position $pos2
  * @param Block $block
  */
  public static function fillAreaWithBlock(Position $pos1, Position $pos2, Block $block): void {
    self::setAreaBlocks($pos1, $pos2, $block);
  }

  /**
  * Clear the entire area defined by two positions, replacing all blocks with air.
  * @param Position $pos1
  * @param Position $pos2
  */
  public static function clearArea(Position $pos1, Position $pos2): void {
    $air = VanillaBlocks::AIR();
    self::setAreaBlocks($pos1, $pos2, $air);
  }

  /**
  * Replace a specific type of block with another within the area defined by two positions.
  * @param Position $pos1
  * @param Position $pos2
  * @param Block $targetBlock
  * @param Block $replacementBlock
  */
  public static function replaceBlocksInArea(Position $pos1, Position $pos2, Block $targetBlock, Block $replacementBlock): void {
    $blocks = self::getAreaBlocks($pos1, $pos2);
    $world = $pos1->getWorld();

    foreach ($blocks as $block) {
      if ($block->getId() === $targetBlock->getId()) {
        $world->setBlockAt($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ(), $replacementBlock);
      }
    }
  }

  /**
  * Count the number of specific blocks within the area defined by two positions.
  * @param Position $pos1
  * @param Position $pos2
  * @param Block $targetBlock
  * @return int
  */
  public static function countBlocksInArea(Position $pos1, Position $pos2, Block $targetBlock): int {
    $blocks = self::getAreaBlocks($pos1, $pos2);
    $count = 0;

    foreach ($blocks as $block) {
      if ($block->getId() === $targetBlock->getId()) {
        $count++;
      }
    }

    return $count;
  }

  /**
  * Validate that both positions are in the same world and have a world assigned.
  * @param Position $pos1
  * @param Position $pos2
  * @throws \InvalidArgumentException if the positions are not in the same world or if a world is not assigned.
  */
  private static function validatePositions(Position $pos1, Position $pos2): void {
    if ($pos1->getWorld() === null || $pos2->getWorld() === null) {
      throw new \InvalidArgumentException('Both positions must have a world assigned.');
    }

    if ($pos1->getWorld()->getFolderName() !== $pos2->getWorld()->getFolderName()) {
      throw new \InvalidArgumentException('Both positions must be in the same world.');
    }
  }

  /**
  * Validate that the blocks parameter is either a Block instance or an array of Block instances.
  * @param mixed $blocks
  * @throws \InvalidArgumentException if blocks are not valid.
  */
  private static function validateBlocks($blocks): void {
    if (!is_array($blocks) && !$blocks instanceof Block) {
      throw new \InvalidArgumentException('Blocks must be either a Block instance or an array of Block instances.');
    }

    if (is_array($blocks)) {
      foreach ($blocks as $block) {
        if (!$block instanceof Block) {
          throw new \InvalidArgumentException('All elements in the blocks array must be instances of Block.');
        }
      }
    }
  }
}