<?php

declare(strict_types = 1);

/**
* Class crashdump
*/
final class crashdump {
  /**
  * crashdump constructor.
  * @param Throwable $error The object representing the error.
  */
  public function __construct(private \Throwable $error) {
    $this->logError();
  }

  /**
  * Log the error information.
  */
  private function logError(): void {
    $logger = Library::getInstance()->getLogger();
    $logger->error($this->getContent());
  }

  /**
  * Get the content of the crash dump.
  *
  * @return string
  */
  private function getContent(): string {
    $message = $this->error->getMessage();
    $file = $this->error->getFile();
    $line = $this->error->getLine();
    $trace = $this->error->getTraceAsString();

    $formattedTrace = "Stack trace:\n" . $trace;

    return 
<<<EOT
Error message: $message
Error location: $file ($line)

$formattedTrace
EOT;
  }
}