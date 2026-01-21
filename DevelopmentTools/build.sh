#!/bin/bash

cd "$(dirname "${BASH_SOURCE[0]}")"
cd ..

echo Checking composer...
composer install --prefer-dist
composer validate --strict
echo outdated:
composer outdated --direct

echo
echo Checking syntax...
vendor/bin/parallel-lint --exclude .git --exclude Support --exclude vendor .

echo
echo Code Analysis...
vendor/bin/phpstan.phar analyse

echo
echo Checking code styles...
vendor/bin/phpcs -sp --standard=ruleset.xml SourceCode
vendor/bin/phpcs -sp --standard=ruleset.tests.xml Tests

vendor/bin/phpunit --config Tests/phpunit.xml

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
