<?php

declare(strict_types = 1);

namespace library\bossbar;

use pocketmine\player\Player;
use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeMap;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

/**
* Class DiverseBossBar
* @package library\bossbar
*/
final class DiverseBossBar extends BossBar {

  /** @var array */
  private array $titles = [];

  /** @var array */
  private array $subTitles = [];

  /** @var AttributeMap[] */
  private array $attributeMaps = [];

  /** @var array */
  private array $colors = [];

  /**
  * DiverseBossBar constructor.
  * Initializes the DiverseBossBar.
  */
  public function __construct() {
    parent::__construct();
  }

  /**
  * Adds a player to the boss bar.
  * @param Player $player The player to add.
  * @return static Returns an instance of DiverseBossBar.
  */
  public function addPlayer(Player $player): static {
    $this->attributeMaps[$player->getId()] = clone parent::getAttributeMap();
    return parent::addPlayer($player);
  }

  /**
  * Removes a player from the boss bar.
  * @param Player $player The player to remove.
  * @return static Returns an instance of DiverseBossBar.
  */
  public function removePlayer(Player $player): static {
    unset($this->attributeMaps[$player->getId()]);
    return parent::removePlayer($player);
  }

  /**
  * Resets the boss bar for a specific player.
  * @param Player $player The player to reset the boss bar for.
  * @return static Returns an instance of DiverseBossBar.
  */
  public function resetFor(Player $player): static {
    unset(
      $this->attributeMaps[$player->getId()],
      $this->titles[$player->getId()],
      $this->subTitles[$player->getId()],
      $this->colors[$player->getId()]
    );
    $this->sendAttributesPacket([$player]);
    $this->sendBossPacket([$player]);
    return $this;
  }

  /**
  * Resets the boss bar for all players.
  * @return static Returns an instance of DiverseBossBar.
  */
  public function resetForAll(): static {
    foreach ($this->getPlayers() as $player) {
      $this->resetFor($player);
    }
    return $this;
  }

  /**
  * Retrieves the title for a specific player.
  *
  * @param Player $player The player to retrieve the title for.
  * @return string Returns the title.
  */
  public function getTitleFor(Player $player): string {
    return $this->titles[$player->getId()] ?? $this->getTitle();
  }

  /**
  * Sets the title for multiple players.
  * @param Player[] $players The players to set the title for.
  * @param string $title The title to set.
  * @return static Returns an instance of DiverseBossBar.
  */
  public function setTitleFor(array $players, string $title = ""): static {
    foreach ($players as $player) {
      $this->titles[$player->getId()] = $title;
      $this->sendBossTextPacket([$player]);
    }
    return $this;
  }

  /**
  * Retrieves the subtitle for a specific player.
  * @param Player $player The player to retrieve the subtitle for.
  * @return string Returns the subtitle.
  */
  public function getSubTitleFor(Player $player): string {
    return $this->subTitles[$player->getId()] ?? $this->getSubTitle();
  }

  /**
  * Sets the subtitle for multiple players.
  * @param Player[] $players The players to set the subtitle for.
  * @param string $subTitle The subtitle to set.
  * @return static Returns an instance of DiverseBossBar.
  */
  public function setSubTitleFor(array $players, string $subTitle = ""): static {
    foreach ($players as $player) {
      $this->subTitles[$player->getId()] = $subTitle;
      $this->sendBossTextPacket([$player]);
    }
    return $this;
  }

  /**
  * Retrieves the full title for a specific player.
  * @param Player $player The player to retrieve the full title for.
  * @return string Returns the full title.
  */
  public function getFullTitleFor(Player $player): string {
    $text = $this->titles[$player->getId()] ?? "";
    if (!empty($this->subTitles[$player->getId()] ?? "")) {
      $text .= "\n\n" . $this->subTitles[$player->getId()] ?? "";
    }
    if (empty($text)) $text = $this->getFullTitle();
    return mb_convert_encoding($text, 'UTF-8');
  }

  /**
  * Sets the health percentage for multiple players.
  * @param Player[] $players The players to set the health percentage for.
  * @param float $percentage The health percentage (between 0 and 1).
  * @return static Returns an instance of DiverseBossBar.
  */
  public function setPercentageFor(array $players, float $percentage): static {
    $percentage = (float) min(1.0, max(0.00, $percentage));
    foreach ($players as $player) {
      $this->getAttributeMap($player)->get(Attribute::HEALTH)->setValue($percentage * $this->getAttributeMap($player)->get(Attribute::HEALTH)->getMaxValue(), true, true);
    }
    $this->sendAttributesPacket($players);
    $this->sendBossHealthPacket($players);
    return $this;
  }

  /**
  * Retrieves the health percentage for a specific player.
  * @param Player $player The player to retrieve the health percentage for.
  * @return float Returns the health percentage.
  */
  public function getPercentageFor(Player $player): float {
    return $this->getAttributeMap($player)->get(Attribute::HEALTH)->getValue() / 100;
  }


  /**
  * Sets the color for multiple players.
  * @param Player[] $players The players to set the color for.
  * @param int $color The color to set.
  * @return static Returns an instance of DiverseBossBar.
  */
  public function setColorFor(array $players, int $color): static {
    foreach ($players as $player) {
      $this->colors[$player->getId()] = $color;
      $this->sendBossPacket([$player]);
    }
    return $this;
  }

  /**
  * Retrieves the color for a specific player.
  * @param Player $player The player to retrieve the color for.
  * @return int Returns the color.
  */
  public function getColorFor(Player $player): int {
    return $this->colors[$player->getId()] ?? $this->getColor();
  }

  /**
  * Displays the boss bar to the specified players.
  * @param Player[] $players The players to show the boss bar to.
  * @TODO Only registered players validation
  */
  public function showTo(array $players): void {
    foreach ($players as $player) {
      if (!$player->isConnected()) continue;
      $player->getNetworkSession()->sendDataPacket(BossEventPacket::show($this->actorId ?? $player->getId(), $this->getFullTitleFor($player), $this->getPercentageFor($player), 1, $this->getColorFor($player)));
    }
  }

  /**
  * Sends a boss packet to the specified players.
  * @param Player[] $players The players to send the boss packet to.
  */
  protected function sendBossPacket(array $players): void {
    foreach ($players as $player) {
      if (!$player->isConnected()) continue;
      $player->getNetworkSession()->sendDataPacket(BossEventPacket::show($this->actorId ?? $player->getId(), $this->getFullTitleFor($player), $this->getPercentageFor($player), 1, $this->getColorFor($player)));
    }
  }

  /**
  * Sends a boss text packet to the specified players.
  * @param Player[] $players The players to send the boss text packet to.
  */
  protected function sendBossTextPacket(array $players): void {
    foreach ($players as $player) {
      if (!$player->isConnected()) continue;
      $player->getNetworkSession()->sendDataPacket(BossEventPacket::title($this->actorId ?? $player->getId(), $this->getFullTitleFor($player)));
    }
  }

  /**
  * Sends an attributes packet to the specified players.
  * @param Player[] $players The players to send the attributes packet to.
  */
  protected function sendAttributesPacket(array $players): void {
    // TODO: might not be needed anymore
    if ($this->actorId === null) return;
    $pk = new UpdateAttributesPacket();
    $pk->actorRuntimeId = $this->actorId;
    foreach ($players as $player) {
      if (!$player->isConnected()) continue;
      $pk->entries = $this->getAttributeMap($player)->needSend();
      $player->getNetworkSession()->sendDataPacket($pk);
    }
  }

  /**
  * Sends a boss health packet to the specified players.
  * @param Player[] $players The players to send the boss health packet to.
  */
  protected function sendBossHealthPacket(array $players): void {
    foreach ($players as $player) {
      if (!$player->isConnected()) continue;
      $player->getNetworkSession()->sendDataPacket(BossEventPacket::healthPercent($this->actorId ?? $player->getId(), $this->getPercentageFor($player)));
    }
  }

  /**
  * Retrieves the attribute map for a specific player.
  * @param Player|null $player The player to retrieve the attribute map for. If null, the default attribute map is returned.
  * @return AttributeMap Returns the attribute map.
  */
  public function getAttributeMap(Player $player = null): AttributeMap {
    if ($player instanceof Player) {
      return $this->attributeMaps[$player->getId()] ?? parent::getAttributeMap();
    }
    return parent::getAttributeMap();
  }

  /**
  * Retrieves the property manager for a specific player.
  * @param Player|null $player The player to retrieve the property manager for. If null, the property manager is set for all players.
  * @return EntityMetadataCollection Returns the property manager.
  */
  public function getPropertyManager(Player $player = null): EntityMetadataCollection {
    $propertyManager = /*clone*/ $this->propertyManager; // TODO: check if memleak
    if ($player instanceof Player) $propertyManager->setString(EntityMetadataProperties::NAMETAG, $this->getFullTitleFor($player));
    else $propertyManager->setString(EntityMetadataProperties::NAMETAG, $this->getFullTitle());
    return $propertyManager;
  }

  /**
  * Returns a string representation of the DiverseBossBar.
  * @return string Returns the string representation.
  */
  public function __toString(): string {
    return __CLASS__ . " ID: $this->actorId, Titles: " . count($this->titles) . ", Subtitles: " . count($this->subTitles) . " [Defaults: " . parent::__toString() . "]";
  }

}