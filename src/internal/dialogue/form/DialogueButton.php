<?php

declare(strict_types = 1);

namespace internal\dialogue\form;

use pocketmine\utils\Utils;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;

/**
* Class DialogueButton
* @package internal\dialogue\form
*/
final class DialogueButton implements \JsonSerializable {

  public const TYPE_URL = 0;
  public const TYPE_COMMAND = 1;
  public const UNKNOWN = 2;
  public const MODE_BUTTON = 0;
  public const MODE_ON_CLOSE = 1;
  public const MODE_ON_ENTER = 2;

  public const CMD_VER = 17;

  /**
  * @var array|null
  */
  protected ?array $data = [];
  
  /**
  * Button constructor.
  */
  public function __construct(
    private ?string $name = '',
    private ?string $text = '',
    private ?array $links = [],
    private ?int $mode = self::MODE_BUTTON,
    private ?int $type = self::TYPE_COMMAND,
    private ?bool $forceClose = false,
    private ?DialogueButtonResponse $buttonResponse = null
  ) {}

  /**
  * @return DialogueButton
  */
  public static function create() : DialogueButton {
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
  * @return string
  */
  public function getName() : self {
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
  * @param bool $forceClose
  * @return $this
  * @throws AssumptionFailedError
  */
  public function setForceCloseOnClick(bool $forceClose) : self {
    if ($this->mode !== self::MODE_BUTTON) {
      throw new AssumptionFailedError("Cannot set force close on click when mode is not button");
    }
    $this->forceCloseOnClick = $forceClose;
    return $this;
  }

  /**
  * @param DialogueButtonResponse $buttonResponse
  * @return $this
  * @throws AssumptionFailedError
  */
  public function setResponse(DialogueButtonResponse $buttonResponse) : self {
    Utils::validateCallableSignature(static function(Player $player, DialogueButton $button) : void {}, $buttonResponse);
    $this->buttonResponse = $buttonResponse;
    return $this;
  }

  /**
  * @return DialogueButtonResponse|null
  */
  public function getResponse() : ?DialogueButtonResponse {
    return $this->buttonResponse;
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