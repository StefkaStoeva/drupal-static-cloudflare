<?php

namespace Drupal\static_generator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\static_generator\Commands\StaticDeployCommand;
use Drupal\tome_static\Form\StaticGeneratorForm;
use Drupal\tome_static\RequestPreparer;
use Drupal\tome_static\StaticGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This form integrates with the Tome Static module for static site generation and provides
 * additional functionality to deploy the generated static files to Cloudflare. It leverages
 * Drupal's Form API, state system, and dependency injection pattern to manage the static
 * generation process and deployment.
 */
class StaticGenerationForm extends FormBase {

  /**
   * The static generator service provided by the Tome Static module.
   *
   * @var \Drupal\tome_static\StaticGeneratorInterface
   */
  protected $static;

  /**
   * The state service for storing persistent state across requests.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Service for preparing the request environment for static generation.
   *
   * @var \Drupal\tome_static\RequestPreparer
   */
  protected $requestPreparer;

  /**
   * A form helper for generating static content using the Tome Static module.
   *
   * @var \Drupal\tome_static\Form\StaticGeneratorForm
   */
  protected $staticGeneratorForm;

  /**
   * Command service for deploying generated static files to Cloudflare.
   *
   * @var \Drupal\static_generator\Commands\StaticDeployCommand
   */
  protected $staticDeployCommand;

  /**
   * Request Stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new StaticGenerationForm object.
   *
   * Initializes the form with necessary services for static site generation and deployment.
   *
   * @param \Drupal\tome_static\StaticGeneratorInterface $static
   *   The static generator service.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\tome_static\RequestPreparer $request_preparer
   *   The request preparer service.
   * @param \Drupal\static_generator\Commands\StaticDeployCommand $staticDeployCommand
   *   The static deploy command service.
   */
  public function __construct(StaticGeneratorInterface $static, StateInterface $state, RequestPreparer $request_preparer, StaticDeployCommand $staticDeployCommand, RequestStack $requestStack) {
    $this->static = $static;
    $this->state = $state;
    $this->requestPreparer = $request_preparer;
    $this->staticGeneratorForm = new StaticGeneratorForm($static, $state, $request_preparer);
    $this->staticDeployCommand = $staticDeployCommand;
    $this->requestStack = $requestStack;
  }

  /**
   * Factory method for creating a new form instance with dependency injection.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   * @return static
   *   A new form instance.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tome_static.generator'),
      $container->get('state'),
      $container->get('tome_static.request_preparer'),
      $container->get('static_generator.static_deploy_command'),
      $container->get('request_stack')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The form ID.
   */
  public function getFormId() {
    return 'static_generator_form';
  }

  /**
   * Builds the form elements.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @return array
   *   The form structure.
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

    $form['actions']['deploy_to_cloudflare'] = [
      '#type' => 'submit',
      '#value' => $this->t('Deploy to Cloudflare'),
      '#submit' => ['::deployToCloudflareSubmit'],
      '#access' => $form_state->get('static_generated') ?: FALSE,
    ];

    return $form;
  }

  /**
   * Form submission handler.
   *
   * Triggers the static site generation process and marks the state as generated.
   *
   * @param array &$form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $current_uri = $this->requestStack->getCurrentRequest()->getRequestUri();
    $form_state->setValue('base_url', $current_uri);
    $this->staticGeneratorForm->submitForm($form, $form_state);
    $form_state->set('static_generated', TRUE);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Custom submit handler for deploying generated static files to Cloudflare.
   *
   * @param array &$form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function deployToCloudflareSubmit(array &$form, FormStateInterface $form_state) {
    $this->staticDeployCommand->deployToCloudflare();
  }
}
