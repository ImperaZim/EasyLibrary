<?php

declare(strict_types = 1);

use library\world\WorldManager;
use library\plugin\PluginToolkit;
use library\bossbar\BossBarHooker;
use library\world\GeneratorHandler;

use pocketmine\utils\SingletonTrait;

use internal\invmenu\InvMenuHandler;
use internal\commando\CommandoHooker;
use internal\dialogue\DialogueHooker;


/**
* Class Library
* TODO: This class should not be called in other plugins!
*/
final class Library extends PluginToolkit {
  use SingletonTrait;
  
  /** @var BossBarHooker */
  protected BossBarHooker $bossbar;

  /** @var InvMenuHandler */
  protected InvMenuHandler $invmenu;

  /** @var DialogueHooker */
  protected DialogueHooker $dialogue;

  /** @var CommandoHooker */
  protected CommandoHooker $commando;

  /** @var GeneratorHandler */
  protected GeneratorHandler $generator;
  
  /**
  * Called when the plugin is loaded.
  */
  protected function onLoad(): void {
    self::setInstance($this);
  }

  /**
  * Called when the plugin is enabled.
  */
  protected function onEnable() : void {
    $this->bossbar = new BossBarHooker($this);
    $this->invmenu = new InvMenuHandler($this);
    $this->dialogue = new DialogueHooker($this);
    $this->commando = new CommandoHooker($this);
    $this->generator = new GeneratorHandler($this);
  }
}