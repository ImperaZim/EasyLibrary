**New Feature: Area Class**

We are excited to introduce a new utility class, `Area`, designed to facilitate the manipulation of blocks within specific areas in the world. This class includes a range of functions that allow for more precise and efficient control over block management. Below is a summary of the new functionalities:

#### Features

- **`getAreaBlocks(Position $pos1, Position $pos2): array`**

  - Retrieves all blocks within the area defined by two positions.
  - Validates that both positions are in the same world and that the world is assigned.

- **`setAreaBlocks(Position $pos1, Position $pos2, $blocks): void`**

  - Sets blocks within the area defined by two positions.
  - Supports both single block and array of blocks.
  - Validates that the blocks are valid instances of `Block`.

- **`fillAreaWithBlock(Position $pos1, Position $pos2, Block $block): void`**

  - Fills the entire area defined by two positions with a specific block.

- **`clearArea(Position $pos1, Position $pos2): void`**

  - Clears the entire area, replacing all blocks within the specified area with air blocks.

- **`replaceBlocksInArea(Position $pos1, Position $pos2, Block $targetBlock, Block $replacementBlock): void`**

  - Replaces a specific type of block with another within the defined area.

- **`countBlocksInArea(Position $pos1, Position $pos2, Block $targetBlock): int`**
  - Counts the number of specific blocks within the area defined by two positions.

#### Validation

- **Position Validation**

  - Ensures that both positions are assigned a world.
  - Confirms that both positions are within the same world.

- **Block Validation**
  - Ensures that the provided blocks are valid instances of `Block`.
  - Supports validation for both single block instances and arrays of blocks.

These new functionalities in the `Area` class provide enhanced control and flexibility for managing blocks within specified areas, enabling more efficient and precise world editing capabilities.
