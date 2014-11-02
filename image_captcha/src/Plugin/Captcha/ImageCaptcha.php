<?php
/**
 * @file
 * Contains \Drupal\image_captcha\Plugin\Captcha\ImageCaptcha.
 */

namespace Drupal\image_captcha\Plugin\Captcha;

use Drupal\captcha\Plugin\CaptchaInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Config\Config;


/**
 * Class ImageCaptcha
 * @package Drupal\image_captcha\Plugin\Captcha
 *
 * @Captcha(
 *   id = "image",
 *   title = @Translation("Image captcha")
 * )
 */
class ImageCaptcha extends PluginBase implements CaptchaInterface, ContainerFactoryPluginInterface {

  /**
   * @var
   */
  protected $request;

  protected $config;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, Request $request, Config $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->request = $request;
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('config.factory')->get('image_captcha.settings')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getSolutionValue($captcha_sid) {
    $allowed_chars = _image_captcha_utf8_split($this->config->get('image_captcha_image_allowed_chars'));
    $code_length = (int) $this->config->get('image_captcha_code_length');
    $code = '';

    for ($i = 0; $i < $code_length; $i++) {
      $code .= $allowed_chars[array_rand($allowed_chars)];
    }

    return $code;
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestionFormElement(array $form, FormStateInterface $form_state, $captcha_sid) {
    return array(
      '#theme' => 'image',
      '#uri' => Url::fromRoute('image_captcha.generator', array(
        'session_id' => $captcha_sid,
        'timestamp' => REQUEST_TIME,
      ))->toString(),
      '#alt' => $this->t('CAPTCHA image')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getAnswerFormElement(array $form, FormStateInterface $form_state) {
    return array(
      '#type' => 'textfield',
      '#title' => t('What code is in the image?'),
      '#description' => t('Enter the characters shown in the image.'),
      '#required' => TRUE,
      '#attributes' => array(
        'autocomplete' => 'off',
      ),
    );
  }

  /**
   * Get description of Captcha challenge.
   *
   * E.g. for Math captcha "Calculate given expression."
   *
   * @return string
   *   Translated captcha challenge description.
   */
  public function getChallengeDescription() {
    return $this->t('This question is for testing whether or not you are a human visitor and to prevent automated spam submissions.');
  }

}