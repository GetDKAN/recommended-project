<?php

namespace Dkan\Composer\Plugin\Symlinker;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Dkan\Composer\Plugin\Symlink\SymlinkerCommand;

/**
 * List of all commands provided by this package.
 *
 * @internal
 */
class CommandProvider implements CommandProviderCapability {

  /**
   * {@inheritdoc}
   */
  public function getCommands() {
    return [new SymlinkerCommand()];
  }

}
