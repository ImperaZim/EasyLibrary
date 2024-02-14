<?php


namespace libraries\playerviewer;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;

class ScoreBoard { 
 use SingletonTrait;
 
 private static $scoreboards = array();

 public function create(Player $player, string $objectiveName, string $displayName) : void {
  if (isset(self::$scoreboards[$player->getName()])) {
   $this->remove($player);
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

 public function remove(Player $player) : void {
  $objectiveName = $this->getObjectiveName($player);
  $pk = new RemoveObjectivePacket();
  $pk->objectiveName = $objectiveName;
  $player->getNetworkSession()->sendDataPacket($pk);
  unset(self::$scoreboards[$player->getName()]);
 }

 public function setLine(Player $player, int $score, string $message) : void {
  if (!isset(self::$scoreboards[$player->getName()])) {
   return;
  }
  if ($score > 15 || $score < 1) {
   error_log("Score must be between the value of 1-15.$score out of range");
   return;
  }
  $objectiveName = $this->getObjectiveName($player);
  $entry = new ScorePacketEntry();
  $entry->objectiveName = $objectiveName;
  $entry->type = $entry::TYPE_FAKE_PLAYER;
  $entry->customName = $message;
  $entry->score = $score;
  $entry->scoreboardId = $score;
  $pk = new SetScorePacket();
  $pk->type = $pk::TYPE_CHANGE;
  $pk->entries[] = $entry;
  $player->getNetworkSession()->sendDataPacket($pk);
 }

 public static function getObjectiveName(Player $player): ?string {
  return isset(self::$scoreboards[$player->getName()]) ? self::$scoreboards[$player->getName()] : "";
 }
 
}