# DKAN Recommended Project Template

Note: This repo is currently under development. Do not use in production!

This is a recommended starting point for a DKAN project.

The suggested set of commands thus far are:
```shell
ddev start
ddev composer install
ddev drush site:install -y
ddev drush pm-enable dkan -y
ddev drush uli
ddev launch
```

In case you wanted to develop the DKAN module locally, you could add in the
following:
```shell
git clone git@github.com:GetDKAN/dkan.git
ddev composer pathrepo ./dkan --package getdkan/dkan
ddev composer update "getdkan/*"
```

Run the PHPUnit tests for DKAN, after creating a site:
```shell
ddev dkan-test-phpunit
```

All of this is subject to improvement.

If you experiment with this repo, you will likely encounter stuff that breaks.
Thank you and please let us know. :-)
