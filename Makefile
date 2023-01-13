all: genautoload fillversion

scope:
	vendor/bin/php-scoper add-prefix

genautoload:
	composer dump-autoload --working-dir build -a

VERSIONFILES := README.MD RoRdbV2.php
VERSION := 0.0.2-0
version:
	sed -i "s/V.V.V-V/$(VERSION)/g" $(VERSIONFILES)

publish_version:
	git checkout -b genrelease
	make version
	git add .
	git commit --signoff -m "Generated version info"
	git checkout master
	git merge --no-ff genrelease -m "Release V$(VERSION)"
	git branch -D genrelease
	git checkout dev
