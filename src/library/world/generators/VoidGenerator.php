<?php

declare(strict_types = 1);

namespace library\world\generators;

use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\generator\Generator;
use library\world\exception\WorldException;

/**
* Class VoidGenerator
* @package library\world\generators
*/
class VoidGenerator extends Generator {

  /**
  * VoidGenerator constructor.
  * @param int $seed The seed for the generator.
  * @param string $preset The generator preset.
  */
  public function __construct(int $seed, string $preset) {
    parent::__construct($seed, $preset);
  }

  /**
  * Generate the contents of a chunk.
  * @param ChunkManager $world The chunk manager.
  * @param int $chunkX The X-coordinate of the chunk.
  * @param int $chunkZ The Z-coordinate of the chunk.
  */
  public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
    try {
      $chunk = $world->getChunk($chunkX, $chunkZ);
      if ($chunkX === 16 && $chunkZ === 16) {
        $chunk->setBlockStateId(0, 64, 0, VanillaBlocks::GRASS()->getStateId());
      }
    } catch (WorldException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Populate the chunk with additional structures or features.
  * @param ChunkManager $world The chunk manager.
  * @param int $chunkX The X-coordinate of the chunk.
  * @param int $chunkZ The Z-coordinate of the chunk.
  */
  public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {}
}