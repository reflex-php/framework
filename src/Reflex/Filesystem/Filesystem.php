<?php
/**
 * Reflex Framework
 *
 * @package   Reflex
 * @version   1.0.0
 * @author    Reflex Community
 * @license   MIT License
 * @copyright 2013 Reflex Community
 * @link      http://reflex.aziri.us/
 */

namespace Reflex\Filesystem;

use FilesystemIterator;
use Reflex\Filesystem\Exceptions\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

/**
 * Filesystem
 *
 * Builds upon Symfony's Filesystem component
 * with a few additional handy features.
 * 
 * @package    Reflex
 * @subpackage Core
 */
class Filesystem extends SymfonyFilesystem
{
    public function remote($path)
    {
        return file_get_contents($path);
    }

    public function get($path)
    {
        if ($this->isFile($path)) {
            return $this->remote($path);
        }

        throw new FileNotFoundException(
            sprintf("File not found at the path %s", $path)
        );
    }

    public function required($path)
    {
        if ($this->isFile($path)) {
            return require $path;
        }

        throw new FileNotFoundException(
            sprintf("File not found at the path %s", $path)
        );
    }

    public function put($path, $contents)
    {
        return file_put_contents($path, $contents);
    }

    public function append($path, $contents)
    {
        return file_put_contents($path, $contents, FILE_APPEND);
    }

    public function delete($path)
    {
        return @unlink($path);
    }

    public function move($path, $destination)
    {
        return copy($path, $destination);
    }

    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function type($path)
    {
        return filetype($path);
    }

    public function size($path)
    {
        return filesize($path);
    }

    public function modified($path)
    {
        return filemtime($path);
    }

    public function isDirectory($directories)
    {
        foreach ($this->toIterator($directories) as $directory) {
            if (is_dir($directory)) {
                continue;
            }

            return false;
        }

        return true;
    }

    public function isWritable($paths)
    {
        foreach ($this->toIterator($paths) as $path) {
            if (is_writable($path)) {
                continue;
            }

            return false;
        }

        return true;
    }

    public function isFile($paths)
    {
        foreach ($this->toIterator($paths) as $path) {
            if (is_file($path)) {
                continue;
            }

            return false;
        }

        return true;
    }

    public function glob($pattern, $flags = 0)
    {
        return glob($pattern, $flags);
    }

    public function files($directory)
    {
        $glob   =   $this->glob($directory . '/*');

        if (false === $glob) {
            return array();
        }

        return array_filter(
            $glob,
            function ($file) {
                return 'file' === filetype($file);
            }
        );
    }

    public function deleteDirectory($directory, $preserve = false)
    {
        if (! $this->isDirectory($directory)) {
            return;
        }

        $items  =   new FilesystemIterator($directory);

        foreach ($items as $item) {
            if ($item->isDir()) {
                $this->deleteDirectory($item->getPathname());
            } else {
                $this->delete($item->getPathname());
            }
        }

        if (false === $preserve) {
            @rmdir($directory);
        }
    }

    public function cleanDirectory($directory)
    {
        return $this->deleteDirectory($directory, true);
    }
}
