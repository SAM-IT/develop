<?php
/**
 * This is a simple file that scans the /projects directory for projects.
 * For each project it tries to identify the entry script using simple heuristics.
 * It then creates a host block for NGINX.
 */

$dir = '/projects';
$blocks = [];
foreach(new DirectoryIterator($dir) as $item) {
    echo $item->getPathName() . "\n";
    if ($item->isDir() && !$item->isDot()) {
        if (null !== $block = createBlock($item->getPathname())) {
            $blocks[$item->getFilename()] = $block;
        }

    }
}

$template = file_get_contents(__DIR__ . '/nginx-settings.conf');
foreach (array_filter($blocks) as $block) {
    echo strtr($template, $block);
};
//var_dump($blocks);

/**
 * Creates a host block from a directory.
 * @param $dir
 */
function createBlock($dir)
{
    if (null !== $root = getWebRoot($dir)) {
        return [
            "{root}" => $root,
            "{entry}" => "$root/index.php",
            "{index}" => "index.php",
            "{project}" => basename($dir)
        ];

    }
}

/**
 * Finds the webroot for a project given its base directory.
 * @param $dir
 */
function getWebRoot($dir) {
    // Check for manifest.
    if (file_exists($dir . '/manifest.json')) {
        return json_decode(file_get_contents($dir . '/manifest.json'), true)['root'];
    } elseif (file_exists($dir .'/index.php')) {
        return $dir;
    } else {
        // Try finding a file named index.php.
        $paths = [];
        $iterator = new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), function(SplFileInfo $current, $key, $iterator) {
            if ($current->getExtension() != 'php') {
                return false;
            }
            return true;
        });

        /** @var SplFileInfo $item */
        foreach(new RecursiveIteratorIterator($iterator) as $item) {
            echo $item->getFilename() . "\n";
            // File must be named index.php and msut not contain vendor or tmp.
            if ($item->getFilename() == 'index.php'
            && strpos($item->getPathname(), 'vendor') === false
            && strpos($item->getPathname(), 'tmp') === false
            ) {
//                echo ".";
                $paths[] = $item->getPathName();
            }
        }
        // Sort the paths to get the most likely entry script.
        usort($paths, function($a, $b) {
            // A path containing the word public is likely what we need.

            // Both contain the word public.
            if (strpos($a, 'public') !== false && strpos($b, 'public') !== false) {
                return strlen($a) < strlen($b) ? -1 : 1;

            // Neither contains the word public.
            } elseif (strpos($a, 'public') === false && strpos($b, 'public') === false) {
                return strlen($a) < strlen($b) ? -1 : 1;
            }

            return strpos($b, 'public') === false ? -1 : 1;
        });
        return isset($paths[0]) ? dirname($paths[0]) : null;
    }


}