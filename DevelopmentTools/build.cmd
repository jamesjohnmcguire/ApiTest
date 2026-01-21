@ECHO OFF

CD %~dp0
CD ..

ECHO Checking composer...
CALL composer install --prefer-dist
CALL composer validate --strict
ECHO outdated:
CALL composer outdated --direct

ECHO Checking syntax...
CALL vendor/bin/parallel-lint --exclude .git --exclude Support --exclude vendor .

ECHO Code Analysis...
CALL vendor\bin\phpstan.phar.bat analyse

ECHO Checking code styles...
CALL php vendor\bin\phpcs -sp --standard=ruleset.xml SourceCode
CALL vendor\bin\phpcs.bat -sp --standard=ruleset.tests.xml Tests

ECHO Running Automated Tests
CD DevelopmentTools
CALL vendor\bin\phpunit --config Tests\phpunit.xml

IF "%1"=="release" GOTO deploy
GOTO finish

:deploy
ECHO Deploying...
if "%~2"=="" GOTO error

IF EXIST digitalzenworks-apitest.zip DEL /Q digitalzenworks-apitest.zip
7z a digitalzenworks-apitest.zip . -x!.git -x!.vscode -x!vendor -x!ApiTest.code-workspace

gh release create v%2 --notes %2 digitalzenworks-apitest.zip
DEL /Q digitalzenworks-apitest.zip

:error
ECHO No version tag supplied for release

:finish
