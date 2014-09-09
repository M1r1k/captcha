<?php

/**
 * @file
 * Contains \Drupal\captcha\Form\CaptchaPointForm.
 */

namespace Drupal\captcha\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\captcha\CaptchaPointInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\captcha\CaptchaPluginManager;

class CaptchaPointForm extends EntityForm implements ContainerInjectionInterface {

  /**
   * @var CaptchaPluginManager
   */
  protected $captchaPluginManager;

  /**
   * Constructor of CaptchaPointForm.
   *
   * @param CaptchaPluginManager $captchaPluginManager
   *   Required for fetching captcha types.
   */
  public function __construct(CaptchaPluginManager $captchaPluginManager) {
    $this->captchaPluginManager = $captchaPluginManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.captcha')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    module_load_include('inc', 'captcha', 'captcha.admin');

    /* @var CaptchaPointInterface $captchaPoint */
    $captcha_point = $this->entity;

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Form ID'),
      '#default_value' => $captcha_point->id(),
    );

    $form['formId'] = array(
      '#type' => 'machine_name',
      '#default_value' => $captcha_point->id(),
      '#machine_name' => array(
        'exists' => 'captcha_point_load',
      ),
      '#disable' => !$captcha_point->isNew(),
    );

    // Select widget for CAPTCHA type.
    $form['captchaType'] = array(
      '#type' => 'select',
      '#title' => t('Challenge type'),
      '#description' => t('The CAPTCHA type to use for this form.'),
      '#default_value' => ($captcha_point->getCaptchaType() ?: $this->config('captcha.settings')->get('captcha_default_challenge')),
      '#options' => array_map(function ($plugin_definition) {
        return $plugin_definition['title'];
      }, $this->captchaPluginManager->getDefinitions()),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $captcha_point = $this->entity;
    $status = $captcha_point->save();

    if ($status) {
      drupal_set_message($this->t('Captcha Point for %label form was saved.', array(
        '%label' => $captcha_point->label(),
      )));
    }
    else {
      drupal_set_message($this->t('Captcha Point for %label form was not saved.', array(
        '%label' => $captcha_point->label(),
      )));
    }
    $form_state->setRedirect('captcha_point.list');
  }
}
