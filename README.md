# DKAN Recommended Project Template

Note: This repo is currently under development. Do not use in production!

This is a recommended starting point for a DKAN project.

The suggested set of commands thus far are:
```shell
# composer create-project getdkan/recommended-project my-project
# Since this isn't yet on packagist, do this instead:
git clone [this repository]
composer install
ddev start
ddev dktl make
ddev dktl install
ddev uli
```

Note also that it's possible to run dktl commands locally without spinning up a bunch of containers like this:
```shell
DKTL_MODE=HOST ./vendor/bin/dktl [your commmand]
```
If you experiment with this repo, you will likely encounter stuff that breaks. Thank you and please let us know. :-)
