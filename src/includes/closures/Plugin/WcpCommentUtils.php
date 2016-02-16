<?php
namespace WebSharks\ZenCache;

/*
 * Automatically clears cache files for a post associated with a particular comment.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `trackback_post` hook.
 * @attaches-to `pingback_post` hook.
 * @attaches-to `comment_post` hook.
 *
 * @param int $comment_id A WordPress comment ID.
 *
 * @return int Total files cleared by this routine (if any).
 */
$self->autoClearCommentPostCache = function ($comment_id) use ($self) {
    $counter = 0; // Initialize.

    if (!($comment_id = (integer) $comment_id)) {
        return $counter; // Nothing to do.
    }
    if (!is_null($done = &$self->cacheKey('autoClearCommentPostCache', $comment_id))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if (!is_object($comment = get_comment($comment_id))) {
        return $counter; // Nothing we can do.
    }
    if (empty($comment->comment_post_ID)) {
        return $counter; // Nothing we can do.
    }
    if ($comment->comment_approved === 'spam' || $comment->comment_approved === '0') {
        // Don't allow next `autoClearPostCache()` call to clear post cache.
        $allow = &$self->cacheKey('autoClearPostCache_allow');
        $allow = false; // Flag as false; i.e., disallow.
        return $counter;
    }
    $counter += $self->autoClearXmlFeedsCache('blog-comments');
    $counter += $self->autoClearXmlFeedsCache('post-comments', $comment->comment_post_ID);
    $counter += $self->autoClearPostCache($comment->comment_post_ID);

    return $counter;
};

/*
 * Automatically clears cache files for a post associated with a particular comment.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `transition_comment_status` hook.
 *
 * @param string    $new_status New comment status.
 * @param string    $old_status Old comment status.
 * @param \stdClass $comment    Comment object.
 *
 * @throws \Exception If a clear failure occurs.
 *
 * @return int Total files cleared by this routine (if any).
 *
 * @note This is also called upon by other routines which listen for
 *    events that are indirectly associated with a comment ID.
 */
$self->autoClearCommentPostCacheTransition = function ($new_status, $old_status, $comment) use ($self) {
    $counter = 0; // Initialize.

    if (!is_object($comment)) {
        return $counter; // Nothing we can do.
    }
    if (empty($comment->comment_post_ID)) {
        return $counter; // Nothing we can do.
    }
    if (!is_null($done = &$self->cacheKey('autoClearCommentPostCacheTransition', array($new_status, $old_status, $comment->comment_post_ID)))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if (!($old_status === 'approved' || ($old_status === 'unapproved' && $new_status === 'approved'))) {
        // If excluded here, don't allow next `autoClearPostCache()` call to clear post cache.
        $allow = &$self->cacheKey('autoClearPostCache_allow');
        $allow = false; // Flag as false; i.e., disallow.
        return $counter;
    }
    $counter += $self->autoClearXmlFeedsCache('blog-comments');
    $counter += $self->autoClearXmlFeedsCache('post-comments', $comment->comment_post_ID);
    $counter += $self->autoClearPostCache($comment->comment_post_ID);

    return $counter;
};
