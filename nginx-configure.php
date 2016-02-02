<?php
/**
 * This is a simple file that scans the /projects directory for projects.
 * For each project it tries to identify the entry script using simple heuristics.
 * It then creates a host block for NGINX.
 */

$dir = '/projects';
$blocks = [];
foreach(new DirectoryIterator($dir) as $item) {
    if ($item->isDir() && !$item->isDot()) {
        if (null !== $block = createBlock($item->getPathname())) {
            $blocks[$item->getFilename()] = $block;
        }

    }
}

$template = file_get_contents(__DIR__ . '/config/nginx-project-template.conf');
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
            "{entry}" => "/index.php",
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
    file_put_contents('php://stderr', "Searching root for $dir\n");
    // Check for manifest.
    if (file_exists($dir . '/manifest.json')) {
        $config = json_decode(file_get_contents($dir . '/manifest.json'), true);
        return is_array($config) && isset($config['root']) ? $config['root'] : null;
    } elseif (file_exists($dir .'/index.php')) {
        return $dir;
    } elseif (file_exists($dir .'/application/index.php')) {
        // Quick check for yii1.
        return $dir .'/application';
    } elseif (file_exists($dir .'/public/index.php')) {
        // Quick check for public/index.php; often used.
        return $dir .'/public';
    } else {
        // Try finding a file named index.php.
        $paths = [];
        $iterator = new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), function(SplFileInfo $current, $key, $iterator) {
            if ($current->isDir() || ($current->getExtension() == 'php')) {
                return true;
            }
            return false;
        });

        /** @var SplFileInfo $item */
        foreach(new RecursiveIteratorIterator($iterator) as $item) {
            // File must be named index.php and msut not contain vendor or tmp.
            if ($item->getFilename() == 'index.php'
            && strpos($item->getPathname(), 'vendor') === false
            && strpos($item->getPathname(), 'tmp') === false
            ) {
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
        if (!isset($paths[0])) {
            file_put_contents('php://stderr', "No root found for $dir\n");
        }
        return isset($paths[0]) ? dirname($paths[0]) : null;
    }


}