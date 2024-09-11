---

**New Feature: PluginCdnResourcePackTrait**

Below are the key usage examples and functions for the `PluginCdnResourcePackTrait`:

### Usage Example: Registering CDN for Resource Packs

To set up a CDN for resource packs with an optional directory and file extension removal:

```php
use imperazim\components\plugin\traits\PluginCdnResourcePackTrait;

class YourPlugin {
    use PluginCdnResourcePackTrait;

    public function onEnable(): void {
        // Setup CDN for resource packs
        $this->registerCdnTextures('https://your-cdn-url.com', 'path/to/textures', true);
    }
}
```

- **`registerCdnTextures(string $source, ?string $target = 'textures', bool $removeExtension = false): void`**

  - Sets up the base CDN URL and directory for resource packs.
  - Optionally removes the file extension from the CDN URL if `removeExtension` is set to `true`.

### Scheduling CDN Matching Task

The trait automatically schedules a delayed task to match resource packs in the CDN directory, ensuring that they are properly mapped:

```php
// Inside the trait
$this->scheduleCdnMatchingTask();
```

- **`scheduleCdnMatchingTask(): void`**

  - Immediately matches resource packs in the CDN directory and schedules a delayed task to match again after 1 tick.

### Matching Resource Packs in the CDN Directory

To map resource packs in the specified CDN directory to their respective URLs:

```php
use pocketmine\Server;
use pocketmine\resourcepacks\ZippedResourcePack;

private function matchPackInCdnDir(): void {
    try {
        $resourcePackManager = Server::getInstance()->getResourcePackManager();
        foreach ($resourcePackManager->getResourceStack() as $pack) {
            // Process each resource pack
        }
    } catch (PluginException $e) {
        // Handle errors
    }
}
```

- **`matchPackInCdnDir(): void`**

  - Processes each zipped resource pack in the specified CDN directory, mapping them to the CDN URL based on the pack UUID and version.
  - Uses the `$removeExtension` flag to optionally remove file extensions from the URLs.

These usage examples and functions form the core of the `PluginCdnResourcePackTrait`, providing easy management of CDN-based resource packs in your Minecraft plugin.

---