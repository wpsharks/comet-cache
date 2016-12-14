<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait FsUtils
{
    /**
     * Normalizes directory/file separators.
     *
     * @since 150422 Rewrite.
     *
     * @param string $dir_file             Directory/file path.
     * @param bool   $allow_trailing_slash Defaults to FALSE.
     *                                     If TRUE; and `$dir_file` contains a trailing slash; we'll leave it there.
     *
     * @return string Normalized directory/file path.
     */
    public function nDirSeps($dir_file, $allow_trailing_slash = false)
    {
        $dir_file = (string) $dir_file;

        if (!isset($dir_file[0])) {
            return ''; // Catch empty string.
        }
        if (mb_strpos($dir_file, '://' !== false)) {
            if (preg_match('/^(?P<stream_wrapper>[a-zA-Z0-9]+)\:\/\//u', $dir_file, $stream_wrapper)) {
                $dir_file = preg_replace('/^(?P<stream_wrapper>[a-zA-Z0-9]+)\:\/\//u', '', $dir_file);
            }
        }
        if (mb_strpos($dir_file, ':' !== false)) {
            if (preg_match('/^(?P<drive_letter>[a-zA-Z])\:[\/\\\\]/u', $dir_file)) {
                $dir_file = preg_replace_callback('/^(?P<drive_letter>[a-zA-Z])\:[\/\\\\]/u', create_function('$m', 'return mb_strtoupper($m[0]);'), $dir_file);
            }
        }
        $dir_file = preg_replace('/\/+/u', '/', str_replace([DIRECTORY_SEPARATOR, '\\', '/'], '/', $dir_file));
        $dir_file = ($allow_trailing_slash) ? $dir_file : rtrim($dir_file, '/'); // Strip trailing slashes.

        if (!empty($stream_wrapper[0])) {
            $dir_file = mb_strtolower($stream_wrapper[0]).$dir_file;
        }
        return $dir_file; // Normalized now.
    }

    /**
     * Acquires system tmp directory path.
     *
     * @since 150422 Rewrite.
     *
     * @return string System tmp directory path; else an empty string.
     */
    public function getTmpDir()
    {
        if (!is_null($dir = &$this->staticKey('getTmpDir'))) {
            return $dir; // Already cached this.
        }
        $possible_dirs = []; // Initialize.

        if (defined('WP_TEMP_DIR')) {
            $possible_dirs[] = (string) WP_TEMP_DIR;
        }
        if ($this->functionIsPossible('sys_get_temp_dir')) {
            $possible_dirs[] = (string) sys_get_temp_dir();
        }
        $possible_dirs[] = (string) ini_get('upload_tmp_dir');

        if (!empty($_SERVER['TEMP'])) {
            $possible_dirs[] = (string) $_SERVER['TEMP'];
        }
        if (!empty($_SERVER['TMPDIR'])) {
            $possible_dirs[] = (string) $_SERVER['TMPDIR'];
        }
        if (!empty($_SERVER['TMP'])) {
            $possible_dirs[] = (string) $_SERVER['TMP'];
        }
        if (mb_stripos(PHP_OS, 'win') === 0) {
            $possible_dirs[] = 'C:/Temp';
        }
        if (mb_stripos(PHP_OS, 'win') !== 0) {
            $possible_dirs[] = '/tmp';
        }
        if (defined('WP_CONTENT_DIR')) {
            $possible_dirs[] = (string) WP_CONTENT_DIR;
        }
        foreach ($possible_dirs as $_key => $_dir) {
            if (($_dir = trim((string) $_dir)) && @is_dir($_dir) && @is_writable($_dir)) {
                return $dir = $this->nDirSeps($_dir);
            }
        }
        unset($_key, $_dir); // Housekeeping.

        return $dir = '';
    }

    /**
     * Finds absolute server path to `/wp-config.php` file.
     *
     * @since 150422 Rewrite.
     *
     * @return string Absolute server path to `/wp-config.php` file;
     *                else an empty string if unable to locate the file.
     */
    public function findWpConfigFile()
    {
        if (!is_null($file = &$this->staticKey('findWpConfigFile'))) {
            return $file; // Already cached this.
        }
        $file = ''; // Initialize.

        if (is_file($abspath_wp_config = ABSPATH.'wp-config.php')) {
            $file = $abspath_wp_config;
        } elseif (is_file($dirname_abspath_wp_config = dirname(ABSPATH).'/wp-config.php')) {
            $file = $dirname_abspath_wp_config;
        }
        return $file;
    }

    /**
     * Adds a tmp name suffix to a directory/file path.
     *
     * @since 150422 Rewrite.
     *
     * @param string $dir_file An input directory or file path.
     *
     * @return string The original `$dir_file` with a tmp name suffix.
     */
    public function addTmpSuffix($dir_file)
    {
        $dir_file = (string) $dir_file;
        $dir_file = rtrim($dir_file, DIRECTORY_SEPARATOR.'\\/');

        return $dir_file.'-'.str_replace('.', '', uniqid('', true)).'-tmp';
    }

    /**
     * Recursive directory iterator based on a regex pattern.
     *
     * @since 150422 Rewrite.
     *
     * @param string $dir   An absolute server directory path.
     * @param string $regex A regex pattern; compares to each full file path.
     *
     * @return \RegexIterator Navigable with {@link \foreach()} where each item
     *                        is a {@link \RecursiveDirectoryIterator}.
     */
    public function dirRegexIteration($dir, $regex = '')
    {
        $dir   = (string) $dir;
        $regex = (string) $regex;

        $dir_iterator      = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_SELF | \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);
        $iterator_iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        if ($regex && !in_array(rtrim(str_replace(['^', '$'], '', $regex), 'ui'), ['/.*/', '/.+/'], true)) { // Apply regex filter?
            return new \RegexIterator($iterator_iterator, $regex, \RegexIterator::MATCH, \RegexIterator::USE_KEY);
        }
        return $iterator_iterator; // Iterate everything.
    }

    /**
     * Abbreviated byte notation for file sizes.
     *
     * @since 151002 Adding a few statistics.
     *
     * @param float $bytes     File size in bytes. A (float) value.
     * @param int   $precision Number of decimals to use.
     *
     * @return string Byte notation.
     */
    public function bytesAbbr($bytes, $precision = 2)
    {
        $bytes     = max(0.0, (float) $bytes);
        $precision = max(0, (int) $precision);
        $units     = ['bytes', 'kbs', 'MB', 'GB', 'TB'];

        $power      = floor(($bytes ? log($bytes) : 0) / log(1024));
        $abbr_bytes = round($bytes / pow(1024, $power), $precision);
        $abbr       = $units[min($power, count($units) - 1)];

        if ($abbr_bytes === (float) 1 && $abbr === 'bytes') {
            $abbr = 'byte'; // Quick fix.
        } elseif ($abbr_bytes === (float) 1 && $abbr === 'kbs') {
            $abbr = 'kb'; // Quick fix.
        }
        return $abbr_bytes.' '.$abbr;
    }

    /**
     * Converts an abbreviated byte notation into bytes.
     *
     * @since 151002 Adding a few statistics.
     *
     * @param string $string A string value in byte notation.
     *
     * @return float A float indicating the number of bytes.
     */
    public function abbrBytes($string)
    {
        $string = (string) $string;
        $regex  = '/^(?P<value>[0-9\.]+)\s*(?P<modifier>bytes|byte|kbs|kb|k|mb|m|gb|g|tb|t)$/i';

        if (!preg_match($regex, $string, $_m)) {
            return (float) 0;
        }
        $value    = (float) $_m['value'];
        $modifier = mb_strtolower($_m['modifier']);
        unset($_m); // Housekeeping.

        switch ($modifier) {
            case 't':
            case 'tb':
                $value *= 1024;
            // Fall through.
            case 'g':
            case 'gb':
                $value *= 1024;
            // Fall through.
            case 'm':
            case 'mb':
                $value *= 1024;
            // Fall through.
            case 'k':
            case 'kb':
            case 'kbs':
                $value *= 1024;
        }
        return (float) $value;
    }

    /**
     * Directory stats.
     *
     * @since 151002 Adding a few statistics.
     *
     * @param string $dir           An absolute server directory path.
     * @param string $regex         A regex pattern; compares to each full file path.
     * @param bool   $include_paths Include array of all scanned file paths?
     * @param bool   $check_disk    Also check disk statistics?
     * @param bool   $no_cache      Do not read/write cache?
     *
     * @return array Directory stats.
     */
    public function getDirRegexStats($dir, $regex = '', $include_paths = false, $check_disk = true, $no_cache = false)
    {
        $dir        = (string) $dir; // Force string.
        $cache_keys = [$dir, $regex, $include_paths, $check_disk];
        if (!$no_cache && !is_null($stats = &$this->staticKey('getDirRegexStats', $cache_keys))) {
            return $stats; // Already cached this.
        }
        $stats = [
            'total_size'        => 0,
            'total_resources'   => 0,
            'total_links_files' => 0,

            'total_links'   => 0,
            'link_subpaths' => [],

            'total_files'   => 0,
            'file_subpaths' => [],

            'total_dirs'   => 0,
            'dir_subpaths' => [],

            'disk_total_space' => 0,
            'disk_free_space'  => 0,
        ];
        if (!$dir || !is_dir($dir)) {
            return $stats; // Not possible.
        }
        $short_name_lc = mb_strtolower(SHORT_NAME); // Once only.

        foreach ($this->dirRegexIteration($dir, $regex) as $_resource) {
            $_resource_sub_path = $_resource->getSubpathname();
            $_resource_basename = basename($_resource_sub_path);

            if ($_resource_basename === '.DS_Store') {
                continue; // Ignore `.htaccess`.
            }
            if ($_resource_basename === '.htaccess') {
                continue; // Ignore `.htaccess`.
            }
            if (mb_stripos($_resource_sub_path, $short_name_lc.'-') === 0) {
                continue; // Ignore [SHORT_NAME] files in base.
            }
            switch ($_resource->getType()) { // `link`, `file`, `dir`.
                case 'link':
                    if ($include_paths) {
                        $stats['link_subpaths'][] = $_resource_sub_path;
                    }
                    ++$stats['total_resources'];
                    ++$stats['total_links_files'];
                    ++$stats['total_links'];

                    break; // Break switch.

                case 'file':
                    if ($include_paths) {
                        $stats['file_subpaths'][] = $_resource_sub_path;
                    }
                    $stats['total_size'] += $_resource->getSize();
                    ++$stats['total_resources'];
                    ++$stats['total_links_files'];
                    ++$stats['total_files'];

                    break; // Break switch.

                case 'dir':
                    if ($include_paths) {
                        $stats['dir_subpaths'][] = $_resource_sub_path;
                    }
                    ++$stats['total_resources'];
                    ++$stats['total_dirs'];

                    break; // Break switch.
            }
        }
        unset($_resource, $_resource_sub_path, $_resource_basename); // Housekeeping.

        if ($check_disk) { // Check disk also?
            $stats['disk_total_space'] = disk_total_space($dir);
            $stats['disk_free_space']  = disk_free_space($dir);
        }
        return $stats;
    }

    /**
     * Apache `.htaccess` rules that deny public access to the contents of a directory.
     *
     * @since 150422 Rewrite.
     *
     * @type string `.htaccess` fules.
     */
    public $htaccess_deny = "<IfModule authz_core_module>\n\tRequire all denied\n</IfModule>\n<IfModule !authz_core_module>\n\tdeny from all\n</IfModule>";
}
