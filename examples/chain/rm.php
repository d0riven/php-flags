<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpFlags\Parser;
use PhpFlags\Spec\ApplicationSpec;

// example gnu rm
$spec = new ApplicationSpec();
$format = <<<FMT
rm (GNU coreutils) {{VERSION}}
Copyright (C) 2020 Free Software Foundation, Inc.
License GPLv3+: GNU GPL version 3 or later <https://gnu.org/licenses/gpl.html>.
This is free software: you are free to change and redistribute it.
There is NO WARRANTY, to the extent permitted by law.

Written by Paul Rubin, David MacKenzie, Richard M. Stallman,
and Jim Meyering.
FMT;
$spec->version('8.32')->format($format)->clearShort(); // clear short -v flag because duplicate -v option
$isForce = $spec->flag('force')
    ->desc('ignore nonexistent files and arguments, never prompt')
    ->short('f')->bool();
$interactive = $spec->flag('interactive')
    ->desc('prompt before every removal')
    ->default('always')
    ->validRule(function ($type) {
        return in_array($type, ['always', 'once', 'never'], true);
    })
    ->string('WHEN');
$noPreserveRoot = $spec->flag('no-preserve-root')
    ->desc("do not treat '/' specially")
    ->bool();
$preserveRoot = $spec->flag('preserve-root')
    ->desc("do not remove '/' (default); with 'all', reject any command line argument on a separate device from its parent")
    ->default('all')->string();
$isRecursive = $spec->flag('recursive')
    ->desc('remove directories and their contents recursively')
    ->short('r')->bool();
$isDir = $spec->flag('dir')
    ->desc('remove empty directories')
    ->short('d')->bool();
$isVerbose = $spec->flag('verbose')->desc('explain what is being done')
    ->short('v')->bool();
$filePaths = $spec->arg()->desc('wanna remove file or directory path')->multiple()
    ->validRule(function (array $filePaths) {
        return array_reduce($filePaths, function ($isFilePath, $filePath) {
            return $isFilePath && file_exists($filePath);
        }, true);
    })
    ->string('FILE');
/*
$ php rm.php -h
Usage:
        php rm.php [FLAG]... (FILE)...

FLAG:
        -f, --force
                ignore nonexistent files and arguments, never prompt

        --interactive[=WHEN]
                prompt before every removal

        --no-preserve-root
                do not treat '/' specially

        --preserve-root[=string]
                do not remove '/' (default); with 'all', reject any command line argument on a
                separate device from its parent

        -r, --recursive
                remove directories and their contents recursively

        -d, --dir
                remove empty directories

        -v, --verbose
                explain what is being done

ARG:
        FILE
                wanna remove file or directory path
 */

/*
$ php rm.php --version
rm (GNU coreutils) 8.32
Copyright (C) 2020 Free Software Foundation, Inc.
License GPLv3+: GNU GPL version 3 or later <https://gnu.org/licenses/gpl.html>.
This is free software: you are free to change and redistribute it.
There is NO WARRANTY, to the extent permitted by law.

Written by Paul Rubin, David MacKenzie, Richard M. Stallman,
and Jim Meyering.
 */
try {
    Parser::create($spec)->parse($argv);
} catch (PhpFlags\InvalidArgumentsException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
} catch (PhpFlags\InvalidSpecException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
}

/*
$ php rm.php test fuga hoge
       isForce: false
   interactive: always
noPreserveRoot: false
   reserveRoot: all
   isRecursive: false
         isDir: false
     isVerbose: false
     filePaths: [test,fuga,hoge]

# all boolean flag is true
$ php rm.php -r -f -v -d foo bar
       isForce: true
   interactive: always
noPreserveRoot: false
  preserveRoot: all
   isRecursive: true
         isDir: true
     isVerbose: true
     filePaths: [foo,bar]

# set all flag value
$ php rm.php -f --interactive=once --no-preserve-root --preserve-root=other -r -d -v foo bar
       isForce: true
   interactive: once
noPreserveRoot: true
  preserveRoot: other
   isRecursive: true
         isDir: true
     isVerbose: true
     filePaths: [foo,bar]

$ php rm.php not_file_path
invalid by validRule. argName:FILE, value:[not_file_path]
*/
echo "       isForce: ", $isForce->get() ? 'true' : 'false', PHP_EOL;
echo "   interactive: ", $interactive->get(), PHP_EOL;
echo "noPreserveRoot: ", $noPreserveRoot->get() ? 'true' : 'false', PHP_EOL;
echo "  preserveRoot: ", $preserveRoot->get() , PHP_EOL;
echo "   isRecursive: ", $isRecursive->get() ? 'true' : 'false', PHP_EOL;
echo "         isDir: ", $isDir->get() ? 'true' : 'false', PHP_EOL;
echo "     isVerbose: ", $isVerbose->get() ? 'true' : 'false', PHP_EOL;
echo "     filePaths: ", sprintf('[%s]', implode(',', $filePaths->get())), PHP_EOL;
