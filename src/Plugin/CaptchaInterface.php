<?php

/**
 * @file
 * Contains \Drupal\captcha\Plugin\CaptchaInterface.
 */

namespace Drupal\captcha\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

interface CaptchaInterface extends PluginInspectionInterface {

  /**
   * @todo place docs here.
   */
  public function getSolutionValue();

  /**
   * Provide form item that contains captcha question.
   *
   * Examples: for Math Captcha it should return text markup
   * with math challenge.
   *
   * @param array $form
   *   Form where captcha will be included.
   * @param FormStateInterface $form_state
   *   Form state of current form.
   *
   * @return array
   *   Piece of form that contains question.
   */
  public function getQuestionFormElement(array $form, FormStateInterface $form_state);

  /**
   * Provide form item that contains input for captcha answer.
   *
   * Examples: for Math Captcha it should return text input.
   *
   * @param array $form
   *   Form where captcha will be included.
   * @param FormStateInterface $form_state
   *   Form state of current form.
   *
   * @return array
   *   Piece of form that contains question.
   */
  public function getAnswerFormElement(array $form, FormStateInterface $form_state);

  /**
   * Contains captcha challenge validation logic.
   *
   * @param array $values
   *   Values array from Form State.
   *
   * @return boolean
   */
  public static function validate(array $values);

  /**
   * Get description of Captcha challenge.
   *
   * E.g. for Math captcha "Calculate given expression."
   *
   * @return string
   *   Translated captcha challenge description.
   */
  public function getChallengeDescription();

}