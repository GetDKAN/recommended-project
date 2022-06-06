<?php

namespace Dkan\Composer\Plugin\Symlinker;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The "drupal:scaffold" command class.
 *
 * Manually run the scaffold operation that normally happens after
 * 'composer install'.
 *
 * @internal
 */
class SymlinkerCommand extends BaseCommand {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('dkan:makesymlinks')
      ->setAliases(['makesymlinks'])
      ->setDescription('Symlink project directories into Drupal.')
      ->setHelp(
        <<<EOT
The <info>drupal:scaffold</info> command places the scaffold files in their
respective locations according to the Symlink stipulated in the composer.json
file.

<info>php composer.phar drupal:scaffold</info>

It is usually not necessary to call <info>drupal:scaffold</info> manually,
because it is called automatically as needed, e.g. after an <info>install</info>
or <info>update</info> command. Note, though, that only packages explicitly
allowed to scaffold in the top-level composer.json will be processed by this
command.

For more information, see https://www.drupal.org/docs/develop/using-composer/using-drupals-composer-scaffold.
EOT
            );

  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $handler = new Handler($this->getComposer(), $this->getIO());
    $handler->makesymlink();
    return 0;
  }

}
