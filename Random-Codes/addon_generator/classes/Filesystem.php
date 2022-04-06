<?php

/**
 * Provides basic utility to manipulate the file system.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Filesystem {
    private static $lastError;

    /**
     * Copies a file.
     *
     * If the target file is older than the origin file, it's always overwritten.
     * If the target file is newer, it is overwritten only when the
     * $overwriteNewerFiles option is set to true.
     *
     * @param string $originFile
     * @param string $targetFile
     * @param bool   $overwriteNewerFiles
     *
     * @throws Exception
     */
    public function copy(string $originFile, string $targetFile, bool $overwriteNewerFiles = FALSE) {
        $originIsLocal = stream_is_local($originFile) || 0 === stripos($originFile, 'file://');
        if ($originIsLocal && !is_file($originFile)) {
            throw new \Exception(sprintf('Failed to copy "%s" because file does not exist.', $originFile));
        }

        $this->mkdir(\dirname($targetFile));

        $doCopy = TRUE;
        if (!$overwriteNewerFiles && NULL === parse_url($originFile, PHP_URL_HOST) && is_file($targetFile)) {
            $doCopy = filemtime($originFile) > filemtime($targetFile);
        }

        if ($doCopy) {
            // https://bugs.php.net/64634
            if (FALSE === $source = @fopen($originFile, 'r')) {
                throw new \Exception(sprintf('Failed to copy "%s" to "%s" because source file could not be opened for reading.', $originFile, $targetFile));
            }

            // Stream context created to allow files overwrite when using FTP stream wrapper - disabled by default
            if (FALSE === $target = @fopen($targetFile, 'w', NULL, stream_context_create(['ftp' => ['overwrite' => TRUE]]))) {
                throw new \Exception(sprintf('Failed to copy "%s" to "%s" because target file could not be opened for writing.', $originFile, $targetFile));
            }

            $bytesCopied = stream_copy_to_stream($source, $target);
            fclose($source);
            fclose($target);
            unset($source, $target);

            if (!is_file($targetFile)) {
                throw new \Exception(sprintf('Failed to copy "%s" to "%s".', $originFile, $targetFile));
            }

            if ($originIsLocal) {
                // Like `cp`, preserve executable permission bits
                @chmod($targetFile, fileperms($targetFile) | (fileperms($originFile) & 0111));

                if ($bytesCopied !== $bytesOrigin = filesize($originFile)) {
                    throw new \Exception(sprintf('Failed to copy the whole content of "%s" to "%s" (%g of %g bytes copied).', $originFile, $targetFile, $bytesCopied, $bytesOrigin));
                }
            }
        }
    }

    /**
     * Creates a directory recursively.
     *
     * @param string|iterable $dirs The directory path
     *
     * @param int             $mode
     *
     * @throws Throwable
     */
    public function mkdir($dirs, int $mode = 0777) {
        foreach ($this->toIterable($dirs) as $dir) {
            if (is_dir($dir)) {
                continue;
            }

            if (!self::box('mkdir', $dir, $mode, TRUE)) {
                if (!is_dir($dir)) {
                    // The directory was not created by a concurrent process. Let's throw an exception with a developer friendly error message if we have one
                    if (self::$lastError) {
                        throw new \Exception(sprintf('Failed to create "%s": ', $dir).self::$lastError);
                    }
                    throw new \Exception(sprintf('Failed to create "%s".', $dir));
                }
            }
        }
    }

    /**
     * Checks the existence of files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to check
     *
     * @return bool true if the file exists, false otherwise
     */
    public function exists($files) {
        $maxPathLength = PHP_MAXPATHLEN - 2;

        foreach ($this->toIterable($files) as $file) {
            if (\strlen($file) > $maxPathLength) {
                throw new \Exception(sprintf('Could not check if file exist because path length exceeds %d characters.', $maxPathLength));
            }

            if (!file_exists($file)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Sets access and modification time of file.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to create
     * @param int|null        $time  The touch time as a Unix timestamp, if not supplied the current system time is used
     * @param int|null        $atime The access time as a Unix timestamp, if not supplied the current system time is used
     *
     * @throws \Exception When touch fails
     */
    public function touch($files, int $time = NULL, int $atime = NULL) {
        foreach ($this->toIterable($files) as $file) {
            $touch = $time ? @touch($file, $time, $atime) : @touch($file);
            if (TRUE !== $touch) {
                throw new \Exception(sprintf('Failed to touch "%s".', $file));
            }
        }
    }

    /**
     * Removes files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to remove
     *
     * @throws \Exception When removal fails
     */
    public function remove($files) {
        if ($files instanceof \Traversable) {
            $files = iterator_to_array($files, FALSE);
        } else if (!\is_array($files)) {
            $files = [$files];
        }
        $files = array_reverse($files);
        foreach ($files as $file) {
            if (is_link($file)) {
                // See https://bugs.php.net/52176
                if (!(self::box('unlink', $file) || '\\' !== \DIRECTORY_SEPARATOR || self::box('rmdir', $file)) && file_exists($file)) {
                    throw new \Exception(sprintf('Failed to remove symlink "%s": ', $file).self::$lastError);
                }
            } else if (is_dir($file)) {
                $this->remove(new \FilesystemIterator($file, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS));

                if (!self::box('rmdir', $file) && file_exists($file)) {
                    throw new \Exception(sprintf('Failed to remove directory "%s": ', $file).self::$lastError);
                }
            } else if (!self::box('unlink', $file) && file_exists($file)) {
                throw new \Exception(sprintf('Failed to remove file "%s": ', $file).self::$lastError);
            }
        }
    }

    /**
     * Change mode for an array of files or directories.
     *
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change mode
     * @param int             $mode      The new mode (octal)
     * @param int             $umask     The mode mask (octal)
     * @param bool            $recursive Whether change the mod recursively or not
     *
     * @throws \Exception When the change fails
     */
    public function chmod($files, int $mode, int $umask = 0000, bool $recursive = FALSE) {
        foreach ($this->toIterable($files) as $file) {
            if (TRUE !== @chmod($file, $mode & ~$umask)) {
                throw new \Exception(sprintf('Failed to chmod file "%s".', $file));
            }
            if ($recursive && is_dir($file) && !is_link($file)) {
                $this->chmod(new \FilesystemIterator($file), $mode, $umask, TRUE);
            }
        }
    }

    /**
     * Change the owner of an array of files or directories.
     *
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change owner
     * @param string|int      $user      A user name or number
     * @param bool            $recursive Whether change the owner recursively or not
     *
     * @throws \Exception When the change fails
     */
    public function chown($files, $user, bool $recursive = FALSE) {
        foreach ($this->toIterable($files) as $file) {
            if ($recursive && is_dir($file) && !is_link($file)) {
                $this->chown(new \FilesystemIterator($file), $user, TRUE);
            }
            if (is_link($file) && \function_exists('lchown')) {
                if (TRUE !== @lchown($file, $user)) {
                    throw new \Exception(sprintf('Failed to chown file "%s".', $file));
                }
            } else {
                if (TRUE !== @chown($file, $user)) {
                    throw new \Exception(sprintf('Failed to chown file "%s".', $file));
                }
            }
        }
    }

    /**
     * Change the group of an array of files or directories.
     *
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change group
     * @param string|int      $group     A group name or number
     * @param bool            $recursive Whether change the group recursively or not
     *
     * @throws \Exception When the change fails
     */
    public function chgrp($files, $group, bool $recursive = FALSE) {
        foreach ($this->toIterable($files) as $file) {
            if ($recursive && is_dir($file) && !is_link($file)) {
                $this->chgrp(new \FilesystemIterator($file), $group, TRUE);
            }
            if (is_link($file) && \function_exists('lchgrp')) {
                if (TRUE !== @lchgrp($file, $group)) {
                    throw new \Exception(sprintf('Failed to chgrp file "%s".', $file));
                }
            } else {
                if (TRUE !== @chgrp($file, $group)) {
                    throw new \Exception(sprintf('Failed to chgrp file "%s".', $file));
                }
            }
        }
    }

    /**
     * Renames a file or a directory.
     *
     * @param string $origin
     * @param string $target
     * @param bool   $overwrite
     *
     * @throws Exception When origin cannot be renamed
     */
    public function rename(string $origin, string $target, bool $overwrite = FALSE) {
        // we check that target does not exist
        if (!$overwrite && $this->isReadable($target)) {
            throw new \Exception(sprintf('Cannot rename because the target "%s" already exists.', $target));
        }

        if (TRUE !== @rename($origin, $target)) {
            if (is_dir($origin)) {
                // See https://bugs.php.net/54097 & https://php.net/rename#113943
                $this->mirror($origin, $target, NULL, ['override' => $overwrite, 'delete' => $overwrite]);
                $this->remove($origin);

                return;
            }
            throw new \Exception(sprintf('Cannot rename "%s" to "%s".', $origin, $target));
        }
    }

    /**
     * Tells whether a file exists and is readable.
     *
     * @param string $filename
     *
     * @return bool
     * @throws Exception When windows path is longer than 258 characters
     */
    private function isReadable(string $filename): bool {
        $maxPathLength = PHP_MAXPATHLEN - 2;

        if (\strlen($filename) > $maxPathLength) {
            throw new \Exception(sprintf('Could not check if file is readable because path length exceeds %d characters.', $maxPathLength));
        }

        return is_readable($filename);
    }

    /**
     * Creates a symbolic link or copy a directory.
     *
     * @param string $originDir
     * @param string $targetDir
     * @param bool   $copyOnWindows
     *
     * @throws Throwable
     */
    public function symlink(string $originDir, string $targetDir, bool $copyOnWindows = FALSE) {
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $originDir = strtr($originDir, '/', '\\');
            $targetDir = strtr($targetDir, '/', '\\');

            if ($copyOnWindows) {
                $this->mirror($originDir, $targetDir);

                return;
            }
        }

        $this->mkdir(\dirname($targetDir));

        if (is_link($targetDir)) {
            if (readlink($targetDir) === $originDir) {
                return;
            }
            $this->remove($targetDir);
        }

        if (!self::box('symlink', $originDir, $targetDir)) {
            $this->linkException($originDir, $targetDir, 'symbolic');
        }
    }

    /**
     * Creates a hard link, or several hard links to a file.
     *
     * @param string          $originFile
     * @param string|string[] $targetFiles The target file(s)
     *
     * @throws Throwable
     */
    public function hardlink(string $originFile, $targetFiles) {
        if (!$this->exists($originFile)) {
            throw new \Exception(NULL);
        }

        if (!is_file($originFile)) {
            throw new \Exception(sprintf('Origin file "%s" is not a file.', $originFile));
        }

        foreach ($this->toIterable($targetFiles) as $targetFile) {
            if (is_file($targetFile)) {
                if (fileinode($originFile) === fileinode($targetFile)) {
                    continue;
                }
                $this->remove($targetFile);
            }

            if (!self::box('link', $originFile, $targetFile)) {
                $this->linkException($originFile, $targetFile, 'hard');
            }
        }
    }

    /**
     * @param string $origin
     * @param string $target
     * @param string $linkType Name of the link type, typically 'symbolic' or 'hard'
     *
     * @throws Exception
     */
    private function linkException(string $origin, string $target, string $linkType) {
        if (self::$lastError) {
            if ('\\' === \DIRECTORY_SEPARATOR && FALSE !== strpos(self::$lastError, 'error code(1314)')) {
                throw new \Exception(sprintf('Unable to create "%s" link due to error code 1314: \'A required privilege is not held by the client\'. Do you have the required Administrator-rights?', $linkType));
            }
        }
        throw new \Exception(sprintf('Failed to create "%s" link from "%s" to "%s".', $linkType, $origin, $target));
    }

    /**
     * Resolves links in paths.
     *
     * With $canonicalize = false (default)
     *      - if $path does not exist or is not a link, returns null
     *      - if $path is a link, returns the next direct target of the link without considering the existence of the target
     *
     * With $canonicalize = true
     *      - if $path does not exist, returns null
     *      - if $path exists, returns its absolute fully resolved final version
     *
     * @param string $path
     * @param bool   $canonicalize
     *
     * @return string|null
     * @throws Exception
     */
    public function readlink(string $path, bool $canonicalize = FALSE) {
        if (!$canonicalize && !is_link($path)) {
            return NULL;
        }

        if ($canonicalize) {
            if (!$this->exists($path)) {
                return NULL;
            }

            if ('\\' === \DIRECTORY_SEPARATOR) {
                $path = readlink($path);
            }

            return realpath($path);
        }

        if ('\\' === \DIRECTORY_SEPARATOR) {
            return realpath($path);
        }

        return readlink($path);
    }

    /**
     * Given an existing path, convert it to a path relative to a given starting path.
     *
     * @param string $endPath
     * @param string $startPath
     *
     * @return string Path of target relative to starting path
     */
    public function makePathRelative(string $endPath, string $startPath) {
        if (!$this->isAbsolutePath($startPath)) {
            throw new InvalidArgumentException(sprintf('The start path "%s" is not absolute.', $startPath));
        }

        if (!$this->isAbsolutePath($endPath)) {
            throw new InvalidArgumentException(sprintf('The end path "%s" is not absolute.', $endPath));
        }

        // Normalize separators on Windows
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $endPath = str_replace('\\', '/', $endPath);
            $startPath = str_replace('\\', '/', $startPath);
        }

        $splitDriveLetter = function ($path) {
            return (\strlen($path) > 2 && ':' === $path[1] && '/' === $path[2] && ctype_alpha($path[0]))
                ? [substr($path, 2), strtoupper($path[0])]
                : [$path, NULL];
        };

        $splitPath = function ($path) {
            $result = [];

            foreach (explode('/', trim($path, '/')) as $segment) {
                if ('..' === $segment) {
                    array_pop($result);
                } else if ('.' !== $segment && '' !== $segment) {
                    $result[] = $segment;
                }
            }

            return $result;
        };

        list($endPath, $endDriveLetter) = $splitDriveLetter($endPath);
        list($startPath, $startDriveLetter) = $splitDriveLetter($startPath);

        $startPathArr = $splitPath($startPath);
        $endPathArr = $splitPath($endPath);

        if ($endDriveLetter && $startDriveLetter && $endDriveLetter != $startDriveLetter) {
            // End path is on another drive, so no relative path exists
            return $endDriveLetter.':/'.($endPathArr ? implode('/', $endPathArr).'/' : '');
        }

        // Find for which directory the common path stops
        $index = 0;
        while (isset($startPathArr[$index]) && isset($endPathArr[$index]) && $startPathArr[$index] === $endPathArr[$index]) {
            ++$index;
        }

        // Determine how deep the start path is relative to the common path (ie, "web/bundles" = 2 levels)
        if (1 === \count($startPathArr) && '' === $startPathArr[0]) {
            $depth = 0;
        } else {
            $depth = \count($startPathArr) - $index;
        }

        // Repeated "../" for each level need to reach the common path
        $traverser = str_repeat('../', $depth);

        $endPathRemainder = implode('/', \array_slice($endPathArr, $index));

        // Construct $endPath from traversing to the common path, then to the remaining $endPath
        $relativePath = $traverser.('' !== $endPathRemainder ? $endPathRemainder.'/' : '');

        return '' === $relativePath ? './' : $relativePath;
    }

    /**
     * Mirrors a directory to another.
     *
     * Copies files and directories from the origin directory into the target directory. By default:
     *
     *  - existing files in the target directory will be overwritten, except if they are newer (see the `override` option)
     *  - files in the target directory that do not exist in the source directory will not be deleted (see the `delete` option)
     *
     * @param string            $originDir
     * @param string            $targetDir
     * @param \Traversable|null $iterator Iterator that filters which files and directories to copy, if null a recursive iterator is created
     * @param array             $options  An array of boolean options
     *                                    Valid options are:
     *                                    - $options['override'] If true, target files newer than origin files are overwritten (see copy(), defaults to false)
     *                                    - $options['copy_on_windows'] Whether to copy files instead of links on Windows (see symlink(), defaults to false)
     *                                    - $options['delete'] Whether to delete files that are not in the source directory (defaults to false)
     *
     * @throws Throwable
     */
    public function mirror(string $originDir, string $targetDir, \Traversable $iterator = NULL, array $options = []) {
        $targetDir = rtrim($targetDir, '/\\');
        $originDir = rtrim($originDir, '/\\');
        $originDirLen = \strlen($originDir);

        if (!$this->exists($originDir)) {
            throw new \Exception(sprintf('The origin directory specified "%s" was not found.', $originDir));
        }

        // Iterate in destination folder to remove obsolete entries
        if ($this->exists($targetDir) && isset($options['delete']) && $options['delete']) {
            $deleteIterator = $iterator;
            if (NULL === $deleteIterator) {
                $flags = \FilesystemIterator::SKIP_DOTS;
                $deleteIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($targetDir, $flags), \RecursiveIteratorIterator::CHILD_FIRST);
            }
            $targetDirLen = \strlen($targetDir);
            foreach ($deleteIterator as $file) {
                $origin = $originDir.substr($file->getPathname(), $targetDirLen);
                if (!$this->exists($origin)) {
                    $this->remove($file);
                }
            }
        }

        $copyOnWindows = $options['copy_on_windows'] ?? FALSE;

        if (NULL === $iterator) {
            $flags = $copyOnWindows ? \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS : \FilesystemIterator::SKIP_DOTS;
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($originDir, $flags), \RecursiveIteratorIterator::SELF_FIRST);
        }

        $this->mkdir($targetDir);
        $filesCreatedWhileMirroring = [];

        foreach ($iterator as $file) {
            if ($file->getPathname() === $targetDir || $file->getRealPath() === $targetDir || isset($filesCreatedWhileMirroring[$file->getRealPath()])) {
                continue;
            }

            $target = $targetDir.substr($file->getPathname(), $originDirLen);
            $filesCreatedWhileMirroring[$target] = TRUE;

            if (!$copyOnWindows && is_link($file)) {
                $this->symlink($file->getLinkTarget(), $target);
            } else if (is_dir($file)) {
                $this->mkdir($target);
            } else if (is_file($file)) {
                $this->copy($file, $target, isset($options['override']) ? $options['override'] : FALSE);
            } else {
                throw new \Exception(sprintf('Unable to guess "%s" file type.', $file));
            }
        }
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param string $file
     *
     * @return bool
     */
    public function isAbsolutePath(string $file) {
        return strspn($file, '/\\', 0, 1)
            || (\strlen($file) > 3 && ctype_alpha($file[0])
                && ':' === $file[1]
                && strspn($file, '/\\', 2, 1)
            )
            || NULL !== parse_url($file, PHP_URL_SCHEME);
    }

    /**
     * Creates a temporary file with support for custom stream wrappers.
     *
     * @param string $dir
     * @param string $prefix The prefix of the generated temporary filename
     *                       Note: Windows uses only the first three characters of prefix
     *
     * @return string The new temporary filename (with path), or throw an exception on failure
     * @throws Exception
     */
    public function tempnam(string $dir, string $prefix) {
        $suffix = \func_num_args() > 2 ? func_get_arg(2) : '';
        list($scheme, $hierarchy) = $this->getSchemeAndHierarchy($dir);

        // If no scheme or scheme is "file" or "gs" (Google Cloud) create temp file in local filesystem
        if ((NULL === $scheme || 'file' === $scheme || 'gs' === $scheme) && '' === $suffix) {
            $tmpFile = @tempnam($hierarchy, $prefix);

            // If tempnam failed or no scheme return the filename otherwise prepend the scheme
            if (FALSE !== $tmpFile) {
                if (NULL !== $scheme && 'gs' !== $scheme) {
                    return $scheme.'://'.$tmpFile;
                }

                return $tmpFile;
            }

            throw new \Exception('A temporary file could not be created.');
        }

        // Loop until we create a valid temp file or have reached 10 attempts
        for ($i = 0; $i < 10; ++$i) {
            // Create a unique filename
            $tmpFile = $dir.'/'.$prefix.uniqid(mt_rand(), TRUE).$suffix;

            // Use fopen instead of file_exists as some streams do not support stat
            // Use mode 'x+' to atomically check existence and create to avoid a TOCTOU vulnerability
            $handle = @fopen($tmpFile, 'x+');

            // If unsuccessful restart the loop
            if (FALSE === $handle) {
                continue;
            }

            // Close the file if it was successfully opened
            @fclose($handle);

            return $tmpFile;
        }

        throw new \Exception('A temporary file could not be created.');
    }

    /**
     * Atomically dumps content into a file.
     *
     * @param string          $filename
     * @param string|resource $content The data to write into the file
     *
     * @throws Throwable
     */
    public function dumpFile(string $filename, $content) {
        if (\is_array($content)) {
            throw new \TypeError(sprintf('Argument 2 passed to "%s()" must be string or resource, array given.', __METHOD__));
        }

        $dir = \dirname($filename);

        if (!is_dir($dir)) {
            $this->mkdir($dir);
        }

        if (!is_writable($dir)) {
            throw new \Exception(sprintf('Unable to write to the "%s" directory.', $dir));
        }

        // Will create a temp file with 0600 access rights
        // when the filesystem supports chmod.
        $tmpFile = $this->tempnam($dir, basename($filename));

        if (FALSE === @file_put_contents($tmpFile, $content)) {
            throw new \Exception(sprintf('Failed to write file "%s".', $filename));
        }

        @chmod($tmpFile, file_exists($filename) ? fileperms($filename) : 0666 & ~umask());

        $this->rename($tmpFile, $filename, TRUE);
    }

    /**
     * Appends content to an existing file.
     *
     * @param string          $filename
     * @param string|resource $content The content to append
     *
     * @throws Throwable
     */
    public function appendToFile(string $filename, $content) {
        if (\is_array($content)) {
            throw new \TypeError(sprintf('Argument 2 passed to "%s()" must be string or resource, array given.', __METHOD__));
        }

        $dir = \dirname($filename);

        if (!is_dir($dir)) {
            $this->mkdir($dir);
        }

        if (!is_writable($dir)) {
            throw new \Exception(sprintf('Unable to write to the "%s" directory.', $dir));
        }

        if (FALSE === @file_put_contents($filename, $content, FILE_APPEND)) {
            throw new \Exception(sprintf('Failed to write file "%s".', $filename));
        }
    }

    private function toIterable($files): iterable {
        return \is_array($files) || $files instanceof \Traversable ? $files : [$files];
    }

    /**
     * Gets a 2-tuple of scheme (may be null) and hierarchical part of a filename (e.g. file:///tmp -> [file, tmp]).
     *
     * @param string $filename
     *
     * @return array
     */
    private function getSchemeAndHierarchy(string $filename): array {
        $components = explode('://', $filename, 2);

        return 2 === \count($components) ? [$components[0], $components[1]] : [NULL, $components[0]];
    }

    /**
     * @param callable $func
     *
     * @return mixed
     * @throws Throwable
     */
    private static function box(callable $func) {
        self::$lastError = NULL;
        set_error_handler(__CLASS__.'::handleError');
        try {
            $result = $func(...\array_slice(\func_get_args(), 1));
            restore_error_handler();

            return $result;
        } catch (\Throwable $e) {
        }
        restore_error_handler();

        throw $e;
    }

    /**
     * @param $type
     * @param $msg
     *
     * @internal
     */
    public static function handleError($type, $msg) {
        self::$lastError = $msg;
    }
}
