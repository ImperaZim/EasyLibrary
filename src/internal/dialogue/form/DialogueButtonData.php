<?php

declare(strict_types = 1);

namespace internal\dialogue\form;

use pocketmine\utils\Utils;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;

/**
* Class DialogueButtonData
* @package internal\dialogue\form
*/
final class DialogueButtonData implements \JsonSerializable {

  public const TYPE_URL = 0;
  public const TYPE_COMMAND = 1;
  public const UNKNOWN = 2;
  public const MODE_BUTTON = 0;
  public const MODE_ON_CLOSE = 1;
  public const MODE_ON_ENTER = 2;

  public const CMD_VER = 17;

  /**
  * @var string
  */
  protected string $name = "";

  /**
  * @var string
  */
  protected string $text = "";

  /**
  * @var array|null
  */
  protected ?array $data = [];

  /**
  * @var int
  */
  protected int $mode = self::MODE_BUTTON;

  /**
  * @var int
  */
  protected int $type = self::TYPE_COMMAND;

  /**
  * @var bool
  */
  protected bool $forceCloseOnClick = false;

  /**
  * @var \Closure|null
  */
  protected ?\Closure $clickHandler = null;

  /**
  * @return DialogueButtonData
  */
  public static function create() : DialogueButtonData {
    return new self;
  }

  /**
  * @param string $name
  * @return $this
  */
  public function setName(string $name) : self {
    $this->name = $name;
    return $this;
  }

  /**
  * @param string $text
  * @return $this
  */
  public function setText(string $text) : self {
    $this->text = $text;
    $this->data = array_map(static fn($str) => [
      "cmd_line" => $str,
      "cmd_ver" => self::CMD_VER
    ], explode("\n", $text));
    return $this;
  }

  /**
  * @param string $link
  * @return $this
  */
  public function addLink(string $link) : self {
    $this->text = $link;
    $this->type = self::TYPE_URL;
    $this->data = null;
    return $this;
  }

  /**
  * @param int $mode
  * @return $this
  */
  public function setMode(int $mode) : self {
    $this->mode = $mode;
    return $this;
  }

  /**
  * @param int $type
  * @return $this
  */
  public function setType(int $type) : self {
    $this->type = $type;
    return $this;
  }

  /**
  * @param bool $forceCloseOnClick
  * @return $this
  * @throws AssumptionFailedError
  */
  public function setForceCloseOnClick(bool $forceCloseOnClick) : self {
    if ($this->mode !== self::MODE_BUTTON) {
      throw new AssumptionFailedError("Cannot set force close on click when mode is not button");
    }
    $this->forceCloseOnClick = $forceCloseOnClick;
    return $this;
  }

  /**
  * @param \Closure $clickHandler
  * @return $this
  * @throws AssumptionFailedError
  */
  public function setClickHandler(\Closure $clickHandler) : self {
    Utils::validateCallableSignature(static function(Player $player) : void {}, $clickHandler);
    $this->clickHandler = $clickHandler;
    return $this;
  }

  /**
  * @return \Closure|null
  */
  public function getClickHandler() : ?\Closure {
    return $this->clickHandler;
  }

  /**
  * @return bool
  */
  public function getForceCloseOnClick() : bool {
    return $this->forceCloseOnClick;
  }

  /**
  * @return array
  */
  public function jsonSerialize() : array {
    return [
      "button_name" => $this->name,
      "text" => $this->text,
      "data" => $this->data,
      "mode" => $this->mode,
      "type" => $this->type
    ];
  }
}