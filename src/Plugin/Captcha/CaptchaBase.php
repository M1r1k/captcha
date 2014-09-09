<?php

/**
 * @file
 * Contains \Drupal\captcha\Plugin\CaptchaBase.
 */

namespace Drupal\captcha\Plugin\Captcha;

use Drupal\captcha\Plugin\CaptchaInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;

abstract class CaptchaBase extends PluginBase implements CaptchaInterface {

  /**
   * {@inheritdoc}
   */
  abstract public function getQuestionFormElement(array $form, FormStateInterface $form_state);

  /**
   * {@inheritdoc}
   */
  abstract public function getAnswerFormElement(array $form, FormStateInterface $form_state);

  /**
   * {@inheritdoc}
   */
  abstract public static function validate(array $values);

  /**
   * {@inheritdoc}
   */
  abstract public function getChallengeDescription();

  abstract public function getQuestionDescription();

}
