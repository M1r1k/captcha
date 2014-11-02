<?php

/**
 * @file
 * Contains CAPTCHA image response class.
 */

namespace Drupal\image_captcha\Controller;

use Drupal\image_captcha\Generator\ImageCaptchaGenerator;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\image_captcha\Response\CaptchaImageResponse;
use Drupal\Core\Config\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptchaImageGeneratorController implements ContainerInjectionInterface {

  /**
   * @var ImageCaptchaGenerator
   */
  protected $generator;

  /**
   * Image Captcha config storage.
   *
   * @var Config
   */
  protected $config;

  /**
   * Watchdog logger channel for captcha.
   *
   * @var LoggerChannelInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(Config $config, LoggerChannelInterface $logger, ImageCaptchaGenerator $generator) {
    $this->config = $config;
    $this->logger = $logger;
    $this->generator = $generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')->get('image_captcha.settings'),
      $container->get('logger.factory')->get('captcha'),
      $container->get('image_captcha.generator')
    );
  }

  /**
   * Main method that throw ImageResponse object to generate image.
   *
   * @return CaptchaImageResponse
   */
  public function _image() {
    return new CaptchaImageResponse($this->config, $this->logger);
  }

  public function image(Request $request) {
    //    $options = $this->container->getParameter('gregwar_captcha.config');
    //    $session = $this->get('session');
    //    $whitelistKey = $options['whitelist_key'];
    //    $isOk = false;
    //
    //    if ($session->has($whitelistKey)) {
    //      $keys = $session->get($whitelistKey);
    //      if (is_array($keys) && in_array($key, $keys)) {
    //        $isOk = true;
    //      }
    //    }
    //
    //    if (!$isOk) {
    //      throw $this->createNotFoundException('Unable to generate a captcha via an URL with this session key.');
    //    }

    /* @var \Gregwar\CaptchaBundle\Generator\CaptchaGenerator $generator */
    //    $generator = $this->container->get('gregwar_captcha.generator');
    //
    //    $persistedOptions = $session->get($key, array());
    //    $options = array_merge($options, $persistedOptions);
    $options = array(
      'width' => 1200,
      'height' => 600,
      'length' => $this->config->get('image_captcha_code_length'),
      'quality' => 100,
      'charset' => $this->config->get('image_captcha_image_allowed_chars'),
      'font' => DRUPAL_ROOT . '/modules/captcha/image_captcha/fonts/Tuffy/Tuffy.ttf',
      'keep_value' => TRUE,
      'bypass_code' => 'pass',
      'distortion' => $this->config->get('image_captcha_distortion_amplitude'),
      'background_color' => [255, 255, 255],
      'interpolation' => $this->config->get('image_captcha_bilinear_interpolation'),
    );

    $session = $request->get('session_id');

    $code = db_query(
      'SELECT solution FROM {captcha_sessions} WHERE csid = :csid AND ip_address = :ip_address',
      array(
        ':csid' => $session,
        ':ip_address' => $request->getClientIp(),
      )
    )->fetchField();

    $options['phrase'] = $code;
    $options['keep_value'] = TRUE;

    $phrase = $this->generator->getPhrase($options);
    $this->generator->setPhrase($phrase);
    $persistedOptions['phrase'] = $phrase;
    //    $session->set($key, $persistedOptions);

    $response = new Response($this->generator->generate($options));
    $response->headers->set('Content-type', 'image/jpeg');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Cache-Control', 'no-cache');

    return $response;
  }

}
