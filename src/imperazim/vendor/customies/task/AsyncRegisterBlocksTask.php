<?php
declare(strict_types=1);

namespace imperazim\vendor\customies\task;

use imperazim\vendor\customies\block\CustomiesBlockFactory;
use pmmp\thread\ThreadSafeArray;
use pocketmine\block\Block;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\scheduler\AsyncTask;

final class AsyncRegisterBlocksTask extends AsyncTask {

	private array $classNames = [];
	private array $serializer = [];
	private array $deserializer = [];

	/**
	 * @param Closure[] $blocks
	 * @phpstan-param array<string, array{(Closure(int): Block), (Closure(BlockStateWriter): Block), (Closure(Block): BlockStateReader)}> $blocks
	 */
	public function __construct(private string $cachePath, array $blockFuncs) {
		$this->classNames = new ThreadSafeArray();
		$this->serializer = new ThreadSafeArray();
		$this->deserializer = new ThreadSafeArray();

		foreach($blockFuncs as $identifier => [$block, $serializer, $deserializer, $className]){
			$this->classNames[$identifier] = $className;
			$this->serializer[$identifier] = $serializer;
			$this->deserializer[$identifier] = $deserializer;
		}
	}

	public function onRun(): void {
		foreach($this->classNames as $identifier => $className){
			// We do not care about the model or creative inventory data in other threads since it is unused outside of
			// the main thread.
			CustomiesBlockFactory::getInstance()->registerBlock($className, $identifier, serializer: $this->serializer[$identifier], deserializer: $this->deserializer[$identifier]);
		}
	}
}
