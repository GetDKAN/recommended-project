<?php

namespace Dkan\Composer\Plugin\Frontend;

use Composer\Command\BaseCommand;
use Composer\Config;
use Composer\Config\JsonConfigSource;
use Composer\Factory;
use Composer\Json\JsonFile;
use Composer\Package\Package;
use Composer\Package\Version\VersionParser;
use Composer\Semver\Constraint\Constraint;
use Composer\Semver\Constraint\MatchAllConstraint;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * The "dkan:frontend:build" command class.
 *
 * @internal
 */
class FrontendBuildCommand extends BaseCommand
{

  protected static $defaultFrontendRepo = [
    'type' => 'vcs',
    'url' => 'https://github.com/GetDKAN/data-catalog-app/',
    'ref' => 'cypress-update-8.7.0',
  ];

  protected static $pluginPrefix = 'dkan_frontend_';

  /**
   * @var Config
   */
  protected $config;

  /**
   * @var JsonConfigSource
   */
  protected $configSource;

  /**
   * Represents the root composer.json file.
   *
   * @var JsonFile
   */
  protected $jsonFile;

  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this
      ->setName('dkan:frontend:build')
      ->setAliases(['frontend-build'])
      ->setDescription('Build frontend repo.')
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
    # Determine whether getdkan/data-catalog-app has been installed.
    $local_repository = $this->getComposer()->getRepositoryManager()->getLocalRepository();
    $frontend_package = $local_repository->findPackage('getdkan/data-catalog-app', new MatchAllConstraint());
    if ($frontend_package === NULL) {
      throw new \Exception('Package getdkan/data-catalog-app has not been installed. Use "composer dkan:frontend:install".');
    }

    # Determine whether npm is available on the system.
    if (!static::externalCommandIsAvailable('npm')) {
      throw new \Exception('NPM is required for this command to operate.');
    }

    # Find getdkan/data-catalog-app install path.
    $frontend_install_path = realpath(
      $this->getComposer()
        ->getInstallationManager()
        ->getInstaller($frontend_package->getType())
        ->getInstallPath($frontend_package)
    );

    $output->writeln('Running yarn install in ' . $frontend_install_path);

    # Run npm install inside package directory.
    $process = new Process(['yarn', 'install'], $frontend_install_path, NULL, NULL, 3600.0);

    $process->run(function ($type, $buffer) use ($output) {
      $output->write($buffer, FALSE);
    });
/*
    $output->writeln('Running npm run build in ' . $frontend_install_path);

    # Run npm install inside package directory.
    $process = new Process(['npm', 'run', 'build', '--force'], $frontend_install_path, NULL, NULL, 3600.0);

    $process->run(function ($type, $buffer) use ($output) {
      $output->write($buffer, FALSE);
    });

*/
    return 0;

    # Glean whether getdkan/dkan is a dependency.
    $local_repository = $this->getComposer()->getRepositoryManager()->getLocalRepository();
    $dkan_package = $local_repository->findPackage('getdkan/dkan', new MatchAllConstraint());

    # Get getdkan/dkan's composer.json extra requirement for the frontend app.
    $dkan_frontend = static::$defaultFrontendRepo;
    if ($dkan_extra = $dkan_package->getExtra() ?? FALSE) {
      if ($dkan_extra['dkan-frontend'] ?? FALSE) {
        $dkan_frontend = $dkan_extra['dkan-frontend'];
        $output->writeln('Using extra.dkan-frontend configuration from DKAN package.');
      }
    }

    # Add repo.
    // @todo: Support more types than vcs.
    if ($dkan_frontend['type'] !== 'vcs') {
      throw new \Exception('Unable to process repo types other than "vcs".');
    }

    $frontend_zip_url = $dkan_frontend['url'] . '/archive/' . $dkan_frontend['ref'] . '.zip';
    $frontend_package = 'getdkan/data-catalog-app';
    $repo = [
      'type' => 'package',
      'package' => [
        'name' => $frontend_package,
        'version' => 'dev-' . $dkan_frontend['ref'],
        'dist' => [
          'url' => $frontend_zip_url,
          'type' => 'zip',
        ],
      ],
    ];

    $this->jsonFile = new JsonFile(Factory::getComposerFile(), null, $this->getIO());
    $config_source = new JsonConfigSource($this->jsonFile);
    $config_source->addRepository(static::$pluginPrefix . hash('crc32', print_r($repo, TRUE)), $repo, false);

    # Add the dependency and constraint.
    // All refs are branches, because there are no Composer-based releases for the frontend app.
    $frontend_constraint = 'dev-' . $dkan_frontend['ref'];

    $package_info = $this->jsonFile->read();
    if (key_exists($frontend_package, $package_info['require'] ?? []) ||
      key_exists($frontend_package, $package_info['require-dev'] ?? [])) {
      $output->writeln($frontend_package . ' is already set as a requirement in the project. Use other Composer tools to change the version requirement as needed.');
      return 0;
    } else {
      $package_info['require'][$frontend_package] = $frontend_constraint;
      $this->jsonFile->write($package_info);
    }

    # Tell the user to perform an update.
    $output->writeln('The composer.json file has been updated with ' . $frontend_package . ':' . $frontend_constraint . '. Perform "composer update" to finish.');
    return 0;
  }

  private static function externalCommandIsAvailable($command)
  {
    $finder = new ExecutableFinder();
    return (bool)$finder->find($command);
  }

}
