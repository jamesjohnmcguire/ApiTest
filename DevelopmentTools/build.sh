#!/bin/bash

cd "$(dirname "${BASH_SOURCE[0]}")"
cd ..

composer validate --strict
composer install --prefer-dist
echo outdated:
composer outdated --direct

echo Checking code styles...
vendor/bin/phpcs -sp --standard=ruleset.xml SourceCode

vendor/bin/phpstan.phar analyse

vendor/bin/phpunit -c Tests/phpunit.xml "$@"

if [[ $1 == "release" ]] ; then
	echo "Release Is Set!"

	# rm -rf Documentation
	# phpDocumentor.phar --setting="graphs.enabled=true" -d SourceCode -t Documentation

	zip -r digitalzenworks-apitest.zip .
	gh release create v$2 --notes $2 digitalzenworks-apitest.zip
	rm digitalzenworks-apitest.zip
fi
