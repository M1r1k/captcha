<?php

/**
 * @file
 *
 */

namespace Drupal\image_captcha\Generator;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

class ImageCaptchaGenerator {

  /**
   * @var CaptchaBuilder
   */
  protected $builder;

  /**
   * @var PhraseBuilder
   */
  protected $phraseBuilder;

  /**
   * @param \Symfony\Component\Routing\RouterInterface $router
   */
  public function __construct(CaptchaBuilder $builder, PhraseBuilder $phraseBuilder) {
    $this->builder          = $builder;
    $this->phraseBuilder    = $phraseBuilder;
  }


  public function getContentType() {
    return 'image/jpeg';
  }

  public function generate($options) {
    $this->builder->setDistortion($options['distortion']);

    $this->builder->setMaxFrontLines($options['max_front_lines']);
    $this->builder->setMaxBehindLines($options['max_behind_lines']);

    if (isset($options['text_color']) && $options['text_color']) {
      if (count($options['text_color']) !== 3) {
        throw new \RuntimeException('text_color should be an array of r, g and b');
      }

      $color = $options['text_color'];
      $this->builder->setTextColor($color[0], $color[1], $color[2]);
    }

    if (isset($options['background_color']) && $options['background_color']) {
      if (count($options['background_color']) !== 3) {
        throw new \RuntimeException('background_color should be an array of r, g and b');
      }

      $color = $options['background_color'];
      $this->builder->setBackgroundColor($color[0], $color[1], $color[2]);
    }

    $this->builder->setInterpolation($options['interpolation']);

    $fingerprint = isset($options['fingerprint']) ? $options['fingerprint'] : NULL;

    $content = $this->builder->build(
      $options['width'],
      $options['height'],
      $options['font'],
      $fingerprint
    )->getGd();

    if ($options['keep_value']) {
      $options['fingerprint'] = $this->builder->getFingerprint();
    }

    //    if (!$options['as_file']) {
    ob_start();
    imagejpeg($content, NULL, $options['quality']);

    return ob_get_clean();
    //    }

    //    return $this->imageFileHandler->saveAsFile($content);
  }

  public function getPhrase($options) {
    // Get the phrase that we'll use for this image
    if ($options['keep_value'] && isset($options['phrase'])) {
      $phrase = $options['phrase'];
    }
    else {
      $phrase = $this->phraseBuilder->build($options['length'], $options['charset']);
      $options['phrase'] = $phrase;
    }

    return $phrase;
  }

  /**
   * Sets the phrase to the builder
   */
  public function setPhrase($phrase) {
    $this->builder->setPhrase($phrase);
  }

}