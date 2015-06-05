<?php
namespace WebSharks\ZenCache;

/*
 * Render admin notices; across all admin dashboard views.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `all_admin_notices` hook.
 */
$self->allAdminNotices = function () use ($self) {
    if (($notices = (is_array($notices = get_option(GLOBAL_NS.'_notices'))) ? $notices : array())) {
        $notices = $updated_notices = array_unique($notices); // De-dupe.

        foreach (array_keys($updated_notices) as $_key) {
            if (strpos($_key, 'persistent-') !== 0) {
                unset($updated_notices[$_key]);
            }
        } // Leave persistent notices; ditch others.
        unset($_key); // Housekeeping after updating notices.

        update_option(GLOBAL_NS.'_notices', $updated_notices);
    }
    if (current_user_can($self->cap)) {
        foreach ($notices as $_key => $_notice) {
            if ($_key === 'persistent-new-pro-version-available') {
                if (!current_user_can($self->update_cap)) {
                    continue; // Not applicable.
                }
            }
            $_dismiss = ''; // Initialize/reset.
            if (strpos($_key, 'persistent-') === 0) {
                $_dismiss_css = 'display:inline-block; float:right; margin:0 0 0 15px; text-decoration:none; font-weight:bold;';
                $_dismiss     = add_query_arg(urlencode_deep(array(GLOBAL_NS => array('dismissNotice' => array('key' => $_key)), '_wpnonce' => wp_create_nonce())));
                $_dismiss     = '<a style="'.esc_attr($_dismiss_css).'" href="'.esc_attr($_dismiss).'">'.__('dismiss &times;', SLUG_TD).'</a>';
            }
            if (strpos($_key, 'class-update-nag') !== false) {
                $_class = 'update-nag';
            } elseif (strpos($_key, 'class-error') !== false) {
                $_class = 'error';
            } else {
                $_class = 'updated';
            }
            echo '<div class="'.$_class.'"><p>'.$_notice.$_dismiss.'</p></div>';
        }
    }
    unset($_key, $_notice, $_dismiss_css, $_dismiss, $_class); // Housekeeping.
};

/*
 * Render admin errors; across all admin dashboard views.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `all_admin_notices` hook.
 */
$self->allAdminErrors = function () use ($self) {
    if (($errors = (is_array($errors = get_option(GLOBAL_NS.'_errors'))) ? $errors : array())) {
        $errors = $updated_errors = array_unique($errors); // De-dupe.

        foreach (array_keys($updated_errors) as $_key) {
            if (strpos($_key, 'persistent-') !== 0) {
                unset($updated_errors[$_key]);
            }
        } // Leave persistent errors; ditch others.
        unset($_key); // Housekeeping after updating notices.

        update_option(GLOBAL_NS.'_errors', $updated_errors);
    }
    if (current_user_can($self->cap)) {
        foreach ($errors as $_key => $_error) {
            $_dismiss = ''; // Initialize/reset.
            if (strpos($_key, 'persistent-') === 0) {
                $_dismiss_css = 'display:inline-block; float:right; margin:0 0 0 15px; text-decoration:none; font-weight:bold;';
                $_dismiss     = add_query_arg(urlencode_deep(array(GLOBAL_NS => array('dismissError' => array('key' => $_key)), '_wpnonce' => wp_create_nonce())));
                $_dismiss     = '<a style="'.esc_attr($_dismiss_css).'" href="'.esc_attr($_dismiss).'">'.__('dismiss &times;', SLUG_TD).'</a>';
            }
            echo '<div class="error"><p>'.$_error.$_dismiss.'</p></div>';
        }
    }
    unset($_key, $_error, $_dismiss_css, $_dismiss); // Housekeeping.
};

/*
 * Enqueue an administrative notice.
 *
 * @since 150422 Rewrite.
 *
 * @param string $notice         HTML markup containing the notice itself.
 * @param string $persistent_key Optional. A unique key which identifies a particular type of persistent notice.
 *                               This defaults to an empty string. If this is passed, the notice is persistent; i.e. it continues to be displayed until dismissed by the site owner.
 * @param bool   $push_to_top    Optional. Defaults to a `FALSE` value.
 *                               If `TRUE`, the notice is pushed to the top of the stack; i.e. displayed above any others.
 */
$self->enqueueNotice = function ($notice, $persistent_key = '', $push_to_top = false) use ($self) {
    $notice         = (string) $notice;
    $persistent_key = (string) $persistent_key;

    $notices = get_option(GLOBAL_NS.'_notices');
    if (!is_array($notices)) {
        $notices = array();
    }
    if ($persistent_key) {
        if (strpos($persistent_key, 'persistent-') !== 0) {
            $persistent_key = 'persistent-'.$persistent_key;
        }
        if ($push_to_top) {
            $notices = array($persistent_key => $notice) + $notices;
        } else {
            $notices[$persistent_key] = $notice;
        }
    } elseif ($push_to_top) {
        array_unshift($notices, $notice);
    } else {
        $notices[] = $notice;
    }
    update_option(GLOBAL_NS.'_notices', $notices);
};

/*
 * Enqueue an administrative error.
 *
 * @since 150422 Rewrite.
 *
 * @param string $error          HTML markup containing the error itself.
 * @param string $persistent_key Optional. A unique key which identifies a particular type of persistent error.
 *                               This defaults to an empty string. If this is passed, the error is persistent; i.e. it continues to be displayed until dismissed by the site owner.
 * @param bool   $push_to_top    Optional. Defaults to a `FALSE` value.
 *                               If `TRUE`, the error is pushed to the top of the stack; i.e. displayed above any others.
 */
$self->enqueueError = function ($error, $persistent_key = '', $push_to_top = false) use ($self) {
    $error          = (string) $error;
    $persistent_key = (string) $persistent_key;

    $errors = get_option(GLOBAL_NS.'_errors');
    if (!is_array($errors)) {
        $errors = array();
    }
    if ($persistent_key) {
        if (strpos($persistent_key, 'persistent-') !== 0) {
            $persistent_key = 'persistent-'.$persistent_key;
        }
        if ($push_to_top) {
            $errors = array($persistent_key => $error) + $errors;
        } else {
            $errors[$persistent_key] = $error;
        }
    } elseif ($push_to_top) {
        array_unshift($errors, $error);
    } else {
        $errors[] = $error;
    }
    update_option(GLOBAL_NS.'_errors', $errors);
};
