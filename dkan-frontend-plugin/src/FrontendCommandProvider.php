<?php

namespace Dkan\Composer\Plugin\Frontend;

use Composer\Plugin\Capability\CommandProvider;

/**
 * List of all commands provided by this package.
 *
 * @internal
 */
class FrontendCommandProvider implements CommandProvider {

  /**
   * {@inheritdoc}
   */
  public function getCommands() {
    return [
      new FrontendCommand(),
      new FrontendBuildCommand(),
    ];
  }

}
