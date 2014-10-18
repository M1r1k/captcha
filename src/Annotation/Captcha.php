<?php

/**
 * @file
 * Contains Drupal\captcha\Annotation\CaptchaPlugin.
 */

namespace Drupal\captcha\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Class CaptchaPlugin defines CaptchaPlugin annotation type.
 *
 * @see CatpchaPluginBase
 *
 * @package Drupal\captcha\Annotation
 *
 * @Annotation
 */
class Captcha extends Plugin {

  /**
   * A unique identifier for the search plugin.
   *
   * @var string
   */
  public $id;

  /**
   * The title for the search page tab.
   *
   * @todo This will potentially be translated twice or cached with the wrong
   *   translation until the search tabs are converted to local task plugins.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $title;

}