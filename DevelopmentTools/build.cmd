CD %~dp0
CD ..

:complete
CALL composer validate --strict
CALL composer install --prefer-dist
ECHO outdated:
CALL composer outdated

ECHO Checking code styles...
php vendor\bin\phpcs -sp --standard=ruleset.xml SourceCode
CALL vendor\bin\phpstan.phar.bat analyse

ECHO Testing...
CD DevelopmentTools
CALL UnitTests.cmd

:finish
