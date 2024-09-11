<?php

declare(strict_types = 1);

namespace imperazim\bugfixes;

use pocketmine\math\Facing;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\block\BlockTypeIds;
use pocketmine\event\entity\EntityDamageEvent;

/**
* Class BugFixesListener
*
* This class handles the bug fixes related to player suffocation in Minecraft.
* Specifically, it listens for suffocation events and tries to prevent the player
* from taking damage by checking if there is space for the player to move.
*
* @package imperazim\bugfixes
*/
final class BugFixesListener implements Listener {

  /**
  * Prevents players from suffocating by detecting if there is enough air around them.
  * If air blocks are found nearby, the player is moved slightly in that direction, and the damage is canceled.
  *
  * @param EntityDamageEvent $event The event triggered when an entity takes damage.
  * @return void
  */
  public function avoidSuffocation(EntityDamageEvent $event) : void {
    // Check if the cause of damage is suffocation
    if ($event->getCause() !== EntityDamageEvent::CAUSE_SUFFOCATION) {
      return;
    }

    $entity = $event->getEntity();

    // Only apply the fix if the entity is a player
    if (!$entity instanceof Player) {
      return;
    }

    $world = $entity->getWorld();
    $pos = $entity->getPosition();

    // Loop through the horizontal directions (North, South, East, West)
    foreach (Facing::HORIZONTAL as $face) {
      // Get the block at the current position's side
      $blockVec = $pos->getSide($face);

      // Check if there are air blocks above and at the player's side
      if (
        $world->getBlock($blockVec->up())->getTypeId() === BlockTypeIds::AIR &&
        $world->getBlock($blockVec)->getTypeId() === BlockTypeIds::AIR
      ) {
        // Move the player slightly toward the air block and cancel the damage
        $entity->setMotion($blockVec->subtractVector($pos)->multiply(0.1));
        $event->cancel();
        return;
      }
    }
  }
}