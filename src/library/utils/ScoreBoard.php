<?php

declare(strict_types = 1);

namespace library\utils;

use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;

/**
* Class ScoreBoard
* @package library\utils
*/
final class ScoreBoard {

  /** @var array */
  private static array $scoreboards = [];

  /**
  * Creates a scoreboard for a player.
  * @param Player $player
  * @param string $objectiveName
  * @param string $displayName
  * @return void
  */
  public static function create(Player $player, string $objectiveName, string $displayName): void {
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
  }

  /**
  * Removes the scoreboard of a player.
  * @param Player $player
  * @return void
  */
  public static function remove(Player $player): void {
    $objectiveName = self::getObjectiveName($player);
    if ($objectiveName !== null) {
      $pk = new RemoveObjectivePacket();
      $pk->objectiveName = $objectiveName;
      $player->getNetworkSession()->sendDataPacket($pk);
      unset(self::$scoreboards[$player->getName()]);
    }
  }

  /**
  * Sets a line on the scoreboard for a player.
  * @param Player $player
  * @param int $score
  * @param string $message
  * @return void
  */
  public static function setLine(Player $player, int $score, string $message): void {
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
  }

  /**
  * Gets the objective name for a player.
  * @param Player $player
  * @return string|null
  */
  public static function getObjectiveName(Player $player): ?string {
    return self::$scoreboards[$player->getName()] ?? null;
  }
}