CD %~dp0
CD ..

:complete
CALL composer validate --strict
CALL composer install --prefer-dist
ECHO outdated:
CALL composer outdated --direct

ECHO Checking syntax...
CALL vendor/bin/parallel-lint --exclude .git --exclude Support --exclude vendor .

ECHO Checking code styles...
php vendor\bin\phpcs -sp --standard=ruleset.xml SourceCode
CALL vendor\bin\phpstan.phar.bat analyse

ECHO Testing...
CD DevelopmentTools
CALL UnitTests.cmd

IF "%1"=="release" GOTO release
GOTO finish

:release
ECHO Release is set...
if "%~2"=="" GOTO error

IF EXIST digitalzenworks-apitest.zip DEL /Q digitalzenworks-apitest.zip
7z a digitalzenworks-apitest.zip . -x!.git -x!.vscode -x!vendor -x!ApiTest.code-workspace
PAUSE
gh release create v%2 --notes %2 digitalzenworks-apitest.zip
DEL /Q digitalzenworks-apitest.zip

:error
ECHO No version tag supplied for release

:finish
