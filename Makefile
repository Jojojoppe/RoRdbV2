all: genautoload fillversion

scope:
	vendor/bin/php-scoper add-prefix

genautoload:
	composer dump-autoload --working-dir build -a

VERSIONFILES := README.MD RoRdbV2.php
VERSION := 0.0.1-0
version:
	sed -i "s/V.V.V-V/$(VERSION)/g" $(VERSIONFILES)
