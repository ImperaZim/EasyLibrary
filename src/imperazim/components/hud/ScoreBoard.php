<?php

declare(strict_types = 1);

namespace imperazim\components\hud;

use GlobalLogger;
use imperazim\components\hud\exception\HudException;

use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;

/**
* Class ScoreBoard
* @package imperazim\components\hud
*/
final class ScoreBoard {
  
  /** @var Array<string, string> */
  public static array $scoreboards = [];

  /**
  * Creates a scoreboard for a player.
  * @param Player $player
  * @param string $objectiveName
  * @param string $displayName
  * @param array|null $lines
  */
  public static function create(Player $player, string $objectiveName, string $displayName, ?array $lines = []): void {
    try {
      if (isset(self::$scoreboards[$player->getName()])) {
        self::remove($player);
      }
      $pk = new SetDisplayObjectivePacket();
      $pk->displaySlot = "sidebar";
      $pk->objectiveName = $objectiveName;
      $pk->displayName = $displayName;
      $pk->criteriaName = "dummy";
      $pk->sortOrder = 0;
      $player->getNetworkSession()->sendDataPacket($pk);
      self::$scoreboards[$player->getName()] = $objectiveName;
      
      if (!empty($lines)) {
        self::setLines($player, $lines);
      }
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Removes the scoreboard of a player.
  * @param Player $player
  */
  public static function remove(Player $player): void {
    try {
      $objectiveName = self::getObjectiveName($player);
      if ($objectiveName !== null) {
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = $objectiveName;
        $player->getNetworkSession()->sendDataPacket($pk);
        unset(self::$scoreboards[$player->getName()]);
      }
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Sets a lines on the scoreboard for a player.
  * @param Player $player
  * @param array|null $lines
  */
  public static function setLines(Player $player, ?array $lines = []): void {
    foreach ($lines as $score => $message) {
      self::setLine($score, $message);
    }
  }

  /**
  * Sets a line on the scoreboard for a player.
  * @param Player $player
  * @param int $score
  * @param string $message
  */
  public static function setLine(Player $player, int $score, string $message): void {
    try {
      if (!isset(self::$scoreboards[$player->getName()])) {
        return;
      }
      if ($score < 1 || $score > 15) {
        error_log("Score must be between the value of 1-15. $score out of range");
        return;
      }
      $objectiveName = self::getObjectiveName($player);
      $entry = new ScorePacketEntry();
      $entry->objectiveName = $objectiveName;
      $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
      $entry->customName = $message;
      $entry->score = $score;
      $entry->scoreboardId = $score;
      $pk = new SetScorePacket();
      $pk->type = SetScorePacket::TYPE_CHANGE;
      $pk->entries[] = $entry;
      $player->getNetworkSession()->sendDataPacket($pk);
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Gets the objective name for a player.
  * @param Player $player
  * @return string|null
  */
  public static function getObjectiveName(Player $player): ?string {
    return self::$scoreboards[$player->getName()] ?? null;
  }

  /**
  * Clears a specific line on the scoreboard for a player.
  * @param Player $player
  * @param int $score
  */
  public static function clearLine(Player $player, int $score): void {
    try {
      self::setLine($player, $score, "");
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Clears all lines on the scoreboard for a player.
  * @param Player $player
  */
  public static function clearAllLines(Player $player): void {
    try {
      $objectiveName = self::getObjectiveName($player);
      if ($objectiveName !== null) {
        for ($i = 1; $i <= 15; $i++) {
          self::clearLine($player, $i);
        }
      }
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Updates the display of the scoreboard for a player.
  * @param Player $player
  */
  public static function updateDisplay(Player $player): void {
    try {
      $objectiveName = self::getObjectiveName($player);
      if ($objectiveName !== null) {
        self::remove($player);
        self::create($player, $objectiveName, $player->getName());
      }
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Sets the title of the scoreboard for a player.
  * @param Player $player
  * @param string $displayName
  */
  public static function setTitle(Player $player, string $displayName): void {
    try {
      $objectiveName = self::getObjectiveName($player);
      if ($objectiveName !== null) {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = "sidebar";
        $pk->objectiveName = $objectiveName;
        $pk->displayName = $displayName;
        $pk->criteriaName = "dummy";
        $pk->sortOrder = 0;
        $player->getNetworkSession()->sendDataPacket($pk);
      }
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }
}