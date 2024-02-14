<?php

namespace libraries\utils;

use pocketmine\world\sound\ClickSound;
use pocketmine\world\sound\XpCollectSound;
use pocketmine\world\sound\Sound as Sounds;
use pocketmine\world\sound\AnvilBreakSound;
use pocketmine\world\sound\EndermanTeleportSound;

/**
 * Class Sound
 * @package libraries\utils
 */
final class Sound {
  
  /**
   * Get the fail sound.
   *
   * @return Sounds
   */
  public static function FAIL(): Sounds {
    return new AnvilBreakSound();
  }
  
  /**
   * Get the click sound.
   *
   * @return Sounds
   */
  public static function CLICK(): Sounds {
    return new ClickSound();
  }
  
  /**
   * Get the success sound.
   *
   * @return Sounds
   */
  public static function SUCCESS(): Sounds {
    return new XpCollectSound();
  }
  
  /**
   * Get the teleport sound.
   *
   * @return Sounds
   */
  public static function TELEPORT(): Sounds {
    return new EndermanTeleportSound();
  }
}
