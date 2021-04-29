# iq_blog

Blog base module for iqual.

Inlcudes:
 - Blog Article Content Type with Node template
 - Views with patterns
 - Blog author role with permissions

 **Submodules**
- iq_blog_comments
Integration of comment Module
Base configuration

- iq_blog_like_dislike
Integration of Like / Disklike button (https://www.drupal.org/project/like_dislike)
Additional FieldFormatter Plugin to only show Like button, without dislike funciton


## Setup & Installation

Install module as usual:

    composer require iqual/iq_blog
    drush en iq_blog


Rebuild CSS:

    drush iq_barrio_helper:sass-compile


Follow installation instructions for entity browsers:
https://github.com/iqual-ch/iq_entity_browsers/blob/8.x-1.x/README.md#iq_entity_browsers (skip step «Install the module using composer»)


If needed:
- Add Blog Artikel as filterable content type in content view
