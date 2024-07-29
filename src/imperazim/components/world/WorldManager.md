**New Feature: WorldManager Class**

We are thrilled to introduce a powerful new utility class, `WorldManager`, designed to streamline and enhance the management of worlds within the server. This class includes a range of functionalities that provide precise and efficient control over world management tasks. Below is a summary of the new functionalities:

#### Features

- **`getWorldPath(string $world): string`**

  - Retrieves the path to the specified world's directory.

- **`worldExists(string $name): bool`**

  - Checks if a world with the given name exists.

- **`getWorld(string $name): ?World`**

  - Retrieves a world by its name, loading it if necessary.

- **`getWorlds(): array`**

  - Returns an array of all loaded worlds.

- **`getDefaultWorld(): ?World`**

  - Gets the default world of the server.

- **`renameWorldName(string $old, string $new): void`**

  - Renames a world from an old name to a new name, handling necessary data updates.

- **`load(string $name): bool`**

  - Loads a world by its name, if it is not already loaded.

- **`unload(string $name): bool`**

  - Unloads a world by its name.

- **`createWorld(string $world, string $generator, mixed $seed): bool`**

  - Creates a new world with the specified name, generator, and seed.

- **`duplicateWorld(string $oldName, string $newWorld): void`**

  - Duplicates an existing world to a new world name.

- **`backupWorld(string $name): bool`**

  - Creates a backup of the specified world.

- **`restoreWorld(string $name, string $backupPath): bool`**

  - Restores a world from a specified backup path.

- **`deleteWorld(string $name): bool`**
  - Deletes a world by its name.

#### Validation

- **World Existence Validation**

  - Ensures that worlds exist before performing operations such as renaming, duplicating, backing up, or deleting.

- **Error Handling**
  - Comprehensive error handling using `WorldException` to manage exceptions and ensure stability during world management operations.

These new functionalities in the `WorldManager` class provide enhanced control and flexibility for managing worlds within the server, enabling more efficient and precise server administration capabilities.
