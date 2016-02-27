<?php
namespace WebSharks\CometCache;

/*
* Unique comment marker.
*
* @since 151220 Enhancing `.htaccess` tweaks.
*
* @return string Used in `.htaccess` parsing.
*/
$self->htaccess_marker = 'WmVuQ2FjaGU';

/*
* Plugin options that have associated htaccess rules.
*
* @since 160103 Improving `.htaccess` tweaks.
*
* @return array Plugin options that have associated htaccess rules
*
* @note We keep track of this to avoid the issue described here: http://git.io/vEFIH
*/
$self->options_with_htaccess_rules = array('cdn_enable');

/*
 * Add template blocks to `/.htaccess` file.
 *
 * @since 151114 Adding `.htaccess` tweaks.
 *
 * @return boolean True if added successfully.
 *
 * @TODO Improve error reporting detail to better catch unexpected failures; see http://git.io/vEFLT
 */
$self->addWpHtaccess = function () use ($self) {
    global $is_apache;

    if (!$is_apache) {
        return false; // Not running the Apache web server.
    }
    if (!$self->options['enable']) {
        return true; // Nothing to do.
    }
    if (!$self->needHtaccessRules()) {
        if($self->findHtaccessMarker()) { // Do we need to clean up previously added rules?
            $self->removeWpHtaccess(); // Fail silently since we don't need rules in place.
        }
        return true; // Nothing to do; no options enabled that require htaccess rules.
    }
    if (!$self->removeWpHtaccess()) {
        return false; // Unable to remove.
    }
    if (!($htaccess = $self->readHtaccessFile())) {
        return false; // Failure; could not read file or invalid UTF8 encountered, file may be corrupt.
    }

    $template_blocks = ''; // Initialize.
    if (is_dir($templates_dir = dirname(dirname(dirname(__FILE__))).'/templates/htaccess')) {
        foreach (scandir($templates_dir) as $_template_file) {
            switch ($_template_file) {
                
            }
        }
        unset($_template_file); // Housekeeping.
    }

    if(empty($template_blocks)) { // Do we need to add anything to htaccess?
        $self->closeHtaccessFile($htaccess); // No need to write to htaccess file in this case.
        return true; // Nothing to do, but no failures either.
    }

    $template_header            = '# BEGIN '.NAME.' '.$self->htaccess_marker.' (the '.$self->htaccess_marker.' marker is required for '.NAME.'; do not remove)'."\n";
    $template_footer            = '# END '.NAME.' '.$self->htaccess_marker;
    $htaccess['file_contents']  = $template_header.trim($template_blocks)."\n".$template_footer."\n\n".$htaccess['file_contents'];

    if (!$self->writeHtaccessFile($htaccess, true)) {
        return false; // Failure; could not write changes.
    }

    return true; // Added successfully.
};

/*
 * Remove template blocks from `/.htaccess` file.
 *
 * @since 151114 Adding `.htaccess` tweaks.
 *
 * @return boolean True if removed successfully.
 *
 * @TODO Improve error reporting detail to better catch unexpected failures; see http://git.io/vEFLT
 */
$self->removeWpHtaccess = function () use ($self) {
    global $is_apache;

    if (!$is_apache) {
        return false; // Not running the Apache web server.
    }
    if (!($htaccess_file = $self->findHtaccessFile())) {
        return true; // File does not exist.
    }
    if (!$self->findHtaccessMarker()) {
        return true; // Template blocks are already gone.
    }
    if (!($htaccess = $self->readHtaccessFile())) {
        return false; // Failure; could not read file, create file, or invalid UTF8 encountered, file may be corrupt.
    }

    $regex                     = '/#\s*BEGIN\s+'.preg_quote(NAME, '/').'\s+'.$self->htaccess_marker.'.*?#\s*END\s+'.preg_quote(NAME, '/').'\s+'.$self->htaccess_marker.'\s*/is';
    $htaccess['file_contents'] = preg_replace($regex, '', $htaccess['file_contents']);

    if (!$self->writeHtaccessFile($htaccess, false)) {
        return false; // Failure; could not write changes.
    }

    return true; // Removed successfully.
};

/*
 * Finds absolute server path to `/.htaccess` file.
 *
 * @since 151114 Adding `.htaccess` tweaks.
 *
 * @return string Absolute server path to `/.htaccess` file;
 *    else an empty string if unable to locate the file.
 */
$self->findHtaccessFile = function () use ($self) {
    $file = ''; // Initialize.
    $home_path = $self->wpHomePath();

    if (is_file($htaccess_file = $home_path.'.htaccess')) {
        $file = $htaccess_file;
    }
    return $file;
};

/*
 * Determines if there are any plugin options enabled that require htaccess rules to be added.
 *
 * @since 160103 Improving `.htaccess` tweaks.
 *
 * @return bool True when an option is enabled that requires htaccess rules, false otherwise.
 */
$self->needHtaccessRules = function () use ($self) {
    if(!is_array($self->options_with_htaccess_rules)) {
        return false; // Nothing to do.
    }
    foreach ($self->options_with_htaccess_rules as $option) {
        if ($self->options[$option]) {
            return true; // Yes, there are options enabled that require htaccess rules.
        }
    }
    return false; // No, there are no options enabled that require htaccess rules.
};

/*
 * Utility method used to check if htaccess file contains $htaccess_marker
 *
 * @since 151114 Adding `.htaccess` tweaks.
 *
 * @param string    $htaccess_marker    Unique comment marker used to identify rules added by this plugin.
 *
 * @return bool False on failure or when marker does not exist in htaccess, true otherwise.
 */
$self->findHtaccessMarker = function ($htaccess_marker = '') use ($self) {
    if (!($htaccess_file = $self->findHtaccessFile())) {
        return false; // File does not exist.
    }
    if (!is_readable($htaccess_file)) {
        return false; // Not possible.
    }
    if (($htaccess_file_contents = file_get_contents($htaccess_file)) === false) {
        return false; // Failure; could not read file.
    }
    if (empty($htaccess_marker)) {
        $htaccess_marker = $self->htaccess_marker;
    }
    if (stripos($htaccess_file_contents, $htaccess_marker) === false) {
        return false; // Htaccess marker is missing
    }

    return true; // Htaccess has the marker
};

/*
 * Gets contents of `/.htaccess` file with exclusive lock to read+write. If file doesn't exist, we attempt to create it.
 *
 * @since 151220 Improving `.htaccess` utils.
 *
 * @param string $htaccess_file     Absolute path to the htaccess file. Optional.
 *                                  If not provided, we attempt to find it or create it if it doesn't exist.
 *
 * @return array|bool Returns an array with data necessary to call $self->writeHtaccessFile():
 *               `fp` a file pointer resource, `file_contents` a string. Returns `false` on failure.
 *
 * @note If a call to this method is not followed by a call to $self->writeHtaccessFile(),
 *       you must make sure that you unlock and close the `fp` resource yourself.
 */
$self->readHtaccessFile = function ($htaccess_file = '') use ($self) {

    if (empty($htaccess_file) && !($htaccess_file = $self->findHtaccessFile())) {
        if (!is_writable($self->wpHomePath()) || file_put_contents($htaccess_file = $self->wpHomePath().'.htaccess', '') === false) {
            return false; // Unable to find and/or create `.htaccess`.
        } // If it doesn't exist, we create the `.htaccess` file here.
    }
    if (!is_readable($htaccess_file) || !is_writable($htaccess_file) || (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)) {
        return false; // Not possible.
    }
    if (!($fp = fopen($htaccess_file, 'rb+')) || !flock($fp, LOCK_EX)) {
        fclose($fp); // Just in case we opened it before failing to obtain a lock.
        return false; // Failure; could not open file and obtain an exclusive lock.
    }
    if (($file_contents = fread($fp, filesize($htaccess_file))) && ($file_contents === wp_check_invalid_utf8($file_contents))) {
        rewind($fp); // Rewind pointer to beginning of file.
        return compact('fp', 'file_contents');
    } else { // Failure; could not read file or invalid UTF8 encountered, file may be corrupt.
        flock($fp, LOCK_UN);
        fclose($fp);
        return false;
    }
};

/*
 * Writes to `/.htaccess` file using provided file pointer.
 *
 * @since 151220 Improving `.htaccess` utils.
 *
 * @param array     $htaccess           Array containing `fp` file resource pointing to htaccess file and `file_contents` to write to file.
 * @param bool      $require_marker     Whether or not to require the marker be present in contents before writing.
 * @param string    $htaccess_marker    Unique comment marker used to identify rules added by this plugin.
 *
 * @return bool True on success, false on failure.
 */
$self->writeHtaccessFile = function (array $htaccess, $require_marker = true, $htaccess_marker = '') use ($self) {

    if (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS) {
        return false; // Not possible.
    }
    if (!is_resource($htaccess['fp'])) {
        return false;
    }
    $htaccess_marker = $htaccess_marker ?: $self->htaccess_marker;

    $_have_marker = stripos($htaccess['file_contents'], $htaccess_marker);

    // Note: rewind() necessary here because we fread() above.
    if (($require_marker && $_have_marker === false) || !rewind($htaccess['fp']) || !ftruncate($htaccess['fp'], 0) || !fwrite($htaccess['fp'], $htaccess['file_contents'])) {
        flock($htaccess['fp'], LOCK_UN);
        fclose($htaccess['fp']);
        return false; // Failure; could not write changes.
    }
    fflush($htaccess['fp']);
    flock($htaccess['fp'], LOCK_UN);
    fclose($htaccess['fp']);

    return true;
};

/*
 * Utility method used to unlock and close htaccess file resource.
 *
 * @since 151114 Adding `.htaccess` tweaks.
 *
 * @param array $htaccess                   Array containing at least an `fp` file resource pointing to htaccess file.
 *
 * @return bool False on failure, true otherwise.
 */
$self->closeHtaccessFile = function (array $htaccess) use ($self) {
    if (!is_resource($htaccess['fp'])) {
        return false; // Failure; requires a valid file resource.
    }
    flock($htaccess['fp'], LOCK_UN);
    fclose($htaccess['fp']);

    return true;
};
