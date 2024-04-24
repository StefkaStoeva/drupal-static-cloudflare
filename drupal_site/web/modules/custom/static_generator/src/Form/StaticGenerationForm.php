<?php

namespace Drupal\static_generator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;
use Drupal\tome_static\Form\StaticGeneratorForm;
use Drupal\tome_static\RequestPreparer;
use Drupal\tome_static\StaticGeneratorInterface;

/**
 * Implements the Static Generator form.
 */
class StaticGenerationForm extends FormBase {

  // use StaticUITrait;

  /**
   * The static generator.
   *
   * @var \Drupal\tome_static\StaticGeneratorInterface
   */
  protected $static;

  /**
   * The state system.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The request preparer.
   *
   * @var \Drupal\tome_static\RequestPreparer
   */
  protected $requestPreparer;

  /**
   * The request preparer.
   *
   * @var \Drupal\tome_static\Form\StaticGeneratorForm
   */
  protected $staticGeneratorForm;

  /**
   * StaticGeneratorForm constructor.
   *
   * @param \Drupal\tome_static\StaticGeneratorInterface $static
   *   The static generator.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state system.
   * @param \Drupal\tome_static\RequestPreparer $request_preparer
   *   The request preparer.
   */
  public function __construct(StaticGeneratorInterface $static, StateInterface $state, RequestPreparer $request_preparer) {
    $this->static = $static;
    $this->state = $state;
    $this->requestPreparer = $request_preparer;
    $this->staticGeneratorForm = new StaticGeneratorForm($static, $state, $request_preparer);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tome_static.generator'),
      $container->get('state'),
      $container->get('tome_static.request_preparer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'static_generator_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Click the button to execute the static generation process with Cloudflare.'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate Static HTML'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setValue('base_url', "https://127.0.0.1/");
    $this->staticGeneratorForm->submitForm($form, $form_state);
    
  }
}
