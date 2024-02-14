<?php 

/**
* Class crashdump
*/
final class crashdump {

  /**
  * crashdump constructor.
  *
  * @param \Throwable $e The throwable object representing the error.
  */
  public function __construct(private \Throwable $e) {
    Plugin::getInstance()->getLogger()->notice($this->getContent());
  }

  /**
  * Get the content of the crash dump.
  *
  * @return string
  */
  private function getContent(): string {
    return sprintf(
      "\nError discriminator: %s\nSource: %s (%d)\nsyntax:\n %s\n",
      $this->e->getMessage(),
      $this->e->getFile(),
      $this->e->getLine(),
      $this->e->getTraceAsString(),
    );
  }
}