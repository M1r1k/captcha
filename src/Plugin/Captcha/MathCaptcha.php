<?php

/**
 * @file
 * Contains Drupal\captcha\Plugin\Captcha\MathCaptcha.
 */

namespace Drupal\captcha\Plugin\Captcha;

use Drupal\catpcha\Plugin\CaptchaBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MathCaptcha.
 *
 * @package Drupal\captcha\Plugin\Captcha
 *
 * @CaptchaPlugin(
 *   id = "math_captcha",
 *   title = @Translation("Math Captcha")
 * )
 */
class MathCaptcha extends CaptchaBase {

  /**
   * First addendum for math expression.
   *
   * @var int
   */
  protected $firstAddendum;

  /**
   * Second addendum for math expression.
   *
   * @var int
   */
  protected $secondAddendum;

  /**
   * {@inheritdoc}
   */
  public function getQuestionFormElement(array $form, FormStateInterface $form_state) {
    return array(
      '#markup' => $this->firstAddendum . ' + ' . $this->secondAddendum
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getAnswerFormElement(array $form, FormStateInterface $form_state) {
    return array(
      '#type' => 'textfield',
      '#title' => t('Math question'),
      '#description' => t('Solve this simple math problem and enter the result. E.g. for 1+3, enter 4.'),
      '#field_prefix' => t('@x + @y = ', array('@x' => $this->firstAddendum, '@y' => $this->secondAddendum)),
      '#size' => 4,
      '#maxlength' => 2,
      '#required' => TRUE,
      '#attributes' => array(
        'autocomplete' => 'off',
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $values) {
    return $values['sum'] == $this->firstAddendum + $this->secondAddendum;
  }

  /**
   * {@inheritdoc}
   */
  public function getChallengeDescription() {
    return $this->t('Solve this simple math problem and enter the result. E.g. for 1+3, enter 4.');
  }

  public function getQuestionDescription() {
    return $this->t('Math');
  }

}
