<?php

namespace Drupal\static_generator\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * This class provides a configuration form for managing the settings related
 * to the Cloudflare integration of the Static Generator module. It extends
 * Drupal's ConfigFormBase, leveraging the configuration management system
 * to store settings such as the Cloudflare Account ID, API Token, and Project Name.
 * These settings are crucial for enabling the deployment of static content
 * to Cloudflare Pages directly from the Drupal site.
 */
class StaticGeneratorSyncConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   *
   * Returns a list of configuration names that are editable by this form.
   *
   * This function specifies which configuration object(s) are managed by the form.
   * The configuration names returned by this function are used for loading
   * and saving the configuration values handled by this form.
   */
  protected function getEditableConfigNames() {
    return [
      'static_generator.static_generator_sync',
    ];
  }

  /**
   * {@inheritdoc}
   *
   * Provides a unique ID for the form.
   *
   * This function returns the form ID string that uniquely identifies the form.
   * The form ID is used in Drupal's Form API to key into the system's form
   * and render arrays, as well as to provide a namespace for form-related URLs,
   * and CSS classes.
   */
  public function getFormId() {
    return 'static_generator_sync_config_form';
  }

  /**
   * {@inheritdoc}
   *
   * Builds the form for configuring Cloudflare integration settings.
   *
   * This method constructs the form elements required for capturing and storing
   * Cloudflare configuration details such as the Account ID, API Token, and Project Name.
   * These settings are essential for enabling communication and deployment actions
   * between the Drupal site and Cloudflare services.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form. This object allows form manipulation during
   *   construction, validation, and submission processes.
   *
   * @return array
   *   The form structure as an associative array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('static_generator.static_generator_sync');

    $form['cloudflare_account_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cloudflare Account ID'),
      '#default_value' => $config->get('cloudflare_account_id'),
      '#required' => TRUE,
    ];

    $form['cloudflare_api_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cloudflare API Token'),
      '#default_value' => $config->get('cloudflare_api_token'),
      '#required' => TRUE,
    ];

    $form['cloudflare_project_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cloudflare project name'),
      '#default_value' => $config->get('cloudflare_project_name'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * Processes the form submission.
   *
   * This method is responsible for handling the form submission event. It captures
   * the user input from the form fields, specifically the Cloudflare Account ID,
   * API Token, and Project Name, and then saves these values to the site's configuration.
   * This ensures that the Cloudflare integration settings are updated with the user's
   * latest inputs. After saving the configuration, it calls the parent class's submitForm
   * method to ensure that any additional submit handlers are executed.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form. This object captures the submitted values
   *   and can be used to alter the form's behavior.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('static_generator.static_generator_sync')
      ->set('cloudflare_account_id', $form_state->getValue('cloudflare_account_id'))
      ->set('cloudflare_api_token', $form_state->getValue('cloudflare_api_token'))
      ->set('cloudflare_project_name', $form_state->getValue('cloudflare_project_name'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
