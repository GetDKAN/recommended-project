<?php

namespace Dkan\Composer\Plugin\Pathrepo;

use Composer\Command\BaseCommand;
use Composer\Config;
use Composer\Config\JsonConfigSource;
use Composer\Factory;
use Composer\Json\JsonFile;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The "dkan:pathrepo" command class.
 *
 * @internal
 */
class PathrepoCommand extends BaseCommand
{

  protected static $pluginPrefix = 'dkan_pathrepo_';

  /**
   * @var Config
   */
  protected $config;

  /**
   * @var JsonConfigSource
   */
  protected $configSource;

  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this
      ->setName('dkan:pathrepo')
      ->setAliases(['pathrepo'])
      ->setDefinition(array(
        new InputArgument('relative_local_path', InputArgument::REQUIRED, 'Relative local path to add to the list of repositories.'),
        new InputOption('unset', null, InputOption::VALUE_NONE, 'Unset the given path repository.'),
        new InputOption('package', null, InputOption::VALUE_OPTIONAL, 'Assumed to be the package in the path repo, will be set to version constraint "@dev".'),
      ))
      ->setDescription('Set a path to be a path repository.')
      ->setHelp(
        <<<EOT
@todo: Improve this documentations.
EOT
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $relative_local_path = $input->getArgument('relative_local_path');
    $repo_package = $input->getOption('package') ?? '';
    $unset = $input->getOption('unset') ?? FALSE;

    // We can't undo a package change, so tell the user they're out of luck on
    // unset.
    if ($unset && $repo_package) {
      $output->writeln('Can\'t undo a package change while unsetting a repo.');
      return 1;
    }

    // remove the dot for our repo name.
    $relative_local_path_pieces = explode('/', $relative_local_path);
    if ($first = $relative_local_path_pieces[0] ?? FALSE) {
      if ($first === '.') {
        unset($relative_local_path_pieces[0]);
      }
    }
    $repo_name = static::$pluginPrefix . implode('.', $relative_local_path_pieces);

    $json_file = new JsonFile(Factory::getComposerFile(), null, $this->getIO());
    $config_source = new JsonConfigSource($json_file);
    $package_info = $json_file->read();

    if ($unset) {
      $removed_repos = [];
      $package_info = $json_file->read();
      // Look for our path and remove, even duplicates.
      if ($repositories = $package_info['repositories'] ?? FALSE) {
        foreach ($repositories as $name => $info) {
          if (($info['url'] ?? '') == $relative_local_path) {
            $config_source->removeRepository($name);
            $removed_repos[] = $name;
          }
        }
      }
      if ($removed_repos) {
        $output->writeln('Removed: ' . implode(', ', $removed_repos));
      } else {
        $output->writeln('Unable to find a repo to remove for path ' . $relative_local_path);
      }
    } else {
      if ($repo = $package_info['repositories'][$repo_name] ?? FALSE) {
        $output->writeln('Repository ' . $repo_name . ' already exists. Taking no further action.');
        return 0;
      }
      // Add our repo.
      $config_source->addRepository($repo_name, ['type' => 'path', 'url' => $relative_local_path], false);
      $output->writeln('Added repo ' . $repo_name . ' for path ' . $relative_local_path);
    }

    if ($repo_package) {
      $package_info = $json_file->read();
      $changed = FALSE;
      if (key_exists($repo_package, $package_info['require'] ?? [])) {
        if ($package_info['require'][$repo_package] !== '@dev') {
          $package_info['require'][$repo_package] = '@dev';
          $output->writeln('Package ' . $repo_package . ' constraint set to @dev.');
          $changed = TRUE;
        } else {
          $output->writeln('Package ' . $repo_package . ' constraint already set to @dev.');
        }
      }
      if (key_exists($repo_package, $package_info['require-dev'] ?? [])) {
        if ($package_info['require-dev'][$repo_package] !== '@dev') {
          $package_info['require-dev'][$repo_package] = '@dev';
          $output->writeln('Package ' . $repo_package . ' constraint set to @dev.');
          $changed = TRUE;
        } else {
          $output->writeln('Package ' . $repo_package . ' constraint already set to @dev.');
        }
      }
      if ($changed) {
        $json_file->write($package_info);
      }
    }

    return 0;
  }

}
