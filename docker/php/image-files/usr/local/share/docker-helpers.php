<?php
class EnvVariableIsRequiredException extends Exception {
    public function __construct(string $envName)
    {
        $message = 'Please, define ' . $envName . ' env variable';
        parent::__construct($message);
    }
}

function getRequiredEnvVariable(string $envName)
{
    $value = getenv($envName);
    if (empty($value)) {
        throw new EnvVariableIsRequiredException($envName);
    }

    return $value;
}

function runCommands(array $steps)
{
    foreach ($steps as $step) {
        runCommand($step);
    }
}

function runCommand($step): int
{
    $resultCode = 0;
    echo 'Running a step "' . $step . '"';
    if (strpos($step, '&', -1) === strlen($step) - 1) {
        echo ' in the background' . "\n";
        exec($step);
    } else {
        echo "\n";
        $isSuccess = system($step, $resultCode);
        if ($isSuccess === false || $resultCode > 0) {
            throw new Exception('Failed step "' . $step . '"');
        }
    }
    return $resultCode;
}

function runComposerCommands(array $afterComposerInstallSteps = []):void
{
    configureTimezone();
    configureAppDir();

    $steps = array_merge(['composer i'], $afterComposerInstallSteps);
    $steps[] = '/usr/local/bin/docker-php-entrypoint';
    $steps[] = 'php-fpm';
    runCommands($steps);
}

function configureAppDir()
{
    $appDir = getRequiredEnvVariable('APP_DIR');
    if (!file_exists($appDir)) {
        throw new Exception('Folder APP_DIR=' . $appDir . ' does not exist');
    }

    chdir($appDir);
}

function configureTimezone()
{
    $timezone = getRequiredEnvVariable('TZ');
    runCommand('echo "' . $timezone . '" | sudo tee /etc/timezone');
}

function configureComposerToken() {
    $githubToken = getRequiredEnvVariable('GITHUB_TOKEN');
    runCommand('composer config -g github-oauth.github.com ' . $githubToken);
}

function waitWhileDBIsCreated(string $host, string $db, string $user, string $password, int $port)
{
    $try = 0;
    while (!($mysqli = @mysqli_connect($host, $user, $password, $db, $port))) {
        echo 'Waiting while db "' . $db . '" is start. Try: ' . $try++ . "\n";
        sleep(1);
    }

    $mysqli->close();
}

function waitWhileAppDbIsMigrated(string $host, string $db, string $user, string $password, int $port, string $table, string $appName)
{
    $mysqli = mysqli_connect($host, $user, $password, $db, $port);
    $try = 1;
    while (!($result = $mysqli->query("SHOW TABLES LIKE '$table'")) || $result->num_rows == 0) {
        echo 'Wait while ' . $appName . ' is migrate. Try: ' . $try++ . "\n";
        sleep(1);
        $mysqli->close();
        $mysqli = mysqli_connect($host, $user, $password, $db, $port);
    }

    $mysqli->close();
}