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

vendor/bin/phpunit -c Tests/phpunit.xml

if [[ $1 == "release" ]] ; then
	echo "Release is set..."

	if [ -z "$2" ] ;then
		echo "No version tag supplied for release"
		exit 1
	fi

	# rm -rf Documentation
	# phpDocumentor.phar --setting="graphs.enabled=true" -d SourceCode -t Documentation

	file="digitalzenworks-apitest.zip"

	if [ -f "$file" ] ; then
		echo "Removing existing zip file..."
		rm "$file"
	fi

	zip -r "$file" . -x ".git/*" -x ".vscode/*" -x "vendor/*" -x "ApiTest.code-workspace"

	gh release create v$2 --notes $2 "$file"
	rm "$file"
fi
