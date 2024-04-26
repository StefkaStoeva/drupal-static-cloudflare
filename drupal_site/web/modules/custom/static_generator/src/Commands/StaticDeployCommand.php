<?php

namespace Drupal\static_generator\Commands;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drush\Commands\DrushCommands;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Messenger\MessengerInterface;

class StaticDeployCommand extends DrushCommands {

  const STATIC_CONTENT_PATH = '/workspaces/drupal-static-cloudflare/drupal_site/html';

  /**
   * Drupal configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new YourCustomCommand object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $configFactory, MessengerInterface $messenger) {
    $this->configFactory = $configFactory;
    $this->messenger = $messenger;
  }

    /**
   * {inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('messenger'),
    );
  }

  /**
   * Deploy static generated content to Cloudflare Pages.
   *
   * @command static_generator:deploy_to_cloudflare
   * @aliases static_generator_dtc
   * @usage static_generator:deploy_to_cloudflare
   *   Deploy to Cloudflare Pages
   */
  public function deployToCloudflare() {
    $config = $this->configFactory->get('static_generator.static_generator_sync');
    $accountId = $config->get('cloudflare_account_id');
    $apiToken = $config->get('cloudflare_api_token');
    $projectName = $config->get('cloudflare_project_name');
    $staticContentPath = self::STATIC_CONTENT_PATH;

    $command = "CLOUDFLARE_ACCOUNT_ID={$accountId} CLOUDFLARE_API_TOKEN={$apiToken} npx wrangler pages deploy {$staticContentPath} --project-name={$projectName}";

    $process = Process::fromShellCommandline($command);
    try {
      $process->mustRun();
      $this->messenger->addMessage(t('Successfully published static content to Cloudflare Pages.'));
    } catch (ProcessFailedException $exception) {
      $this->messenger->addMessage(t('Failed to publish static content: ' . $exception->getMessage()));
    }

    return 0;
  }
}
