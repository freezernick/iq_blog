# iq_blog

Blog base module for iqual.

Inlcudes:
 - Blog Article Content Type with Node template
 - Views with patterns
 - Blog author role with permissions

 **Submodules**
- iq_blog_scheduling\
Base config for scheduling options for Blog posts. Base on iqual-ch/iqual-schedule-cron package


- iq_blog_comments\
Integration of comment module including a base comment type (iq_blog_post_comment) and an answer type (iq_blog_post_comment_answer).

- iq_blog_like_dislike\
Integration of Like / Disklike button (Taken from https://www.drupal.org/project/like_dislike). Additional FieldFormatter Plugin to only show Like button, without dislike funciton. This module automatically installs the iq_like_dislike field to the iq_blog_post content type.

- iq_blog_like_dislike_comment\
This module automatically installs the iq_like_dislike field to the iq_blog_post_comment comment type.

- iq_blog_like_dislike_comment_answer\
This module automatically installs the iq_blog_post_comment_answer field to the iq_blog_post_comment comment type.

## Installation

Install module as usual:

    composer require iqual/iq_blog
    drush en iq_blog

(Optional) Install submodules:

    drush en iq_blog_scheduling

    drush en iq_blog_comment

    drush en iq_blog_like_dislike
    drush en iq_blog_like_dislike_comment
    drush en iq_blog_like_dislike_comment_answer


Add patch for ajax views.

    composer patch-add drupal/ui_patterns "Support AJAX Views / Fix live preview detection" "https://patch-diff.githubusercontent.com/raw/nuvoleweb/ui_patterns/pull/269.diff"


Rebuild CSS:

    drush iq_barrio_helper:sass-compile


Follow installation instructions for entity browsers:
https://github.com/iqual-ch/iq_entity_browsers/blob/8.x-1.x/README.md#iq_entity_browsers (skip step «Install the module using composer»)


## Configuration

Add role iq_blog_author desired users.

If needed: Add Blog Artikel as filterable content type in content view

