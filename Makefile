all: genautoload

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
	git commit --signoff -m "Release V$(VERSION)"
	git checkout master
	git merge --no-ff genrelease -m "Release V$(VERSION)"
	git checkout dev
	git branch -D genrelease

localinstall:
	rm -rf local/nginx_php_mysql_phpmyadmin/web/wp-content/plugins/RoRdbV2/*
	cp -r {build,includes,resources,vendor,composer.json,composer.lock,RoRdbV2.php,scoper.inc.php,README.MD} local/nginx_php_mysql_phpmyadmin/web/wp-content/plugins/RoRdbV2
