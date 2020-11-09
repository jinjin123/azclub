<?php

namespace Drupal\mailgroup_amazonses\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\mailgroup_amazonses\Plugin\Mailgroup\Connection\AmazonSes;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Aws\S3\S3ClientInterface;
use Aws\S3\Exception\S3Exception;
use Drupal\Core\Logger\LoggerChannelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageException;
use Symfony\Component\HttpFoundation\Response;
use Aws\Sns\Message as SnsMessage;
use Aws\Sns\MessageValidator;
use Drupal\mailgroup\Entity\MailGroup;
use Drupal\mailgroup\Entity\MailGroupMessage;
use ZBateson\MailMimeParser\Message;

/**
 * Controller for Mail Group Amazon SES routes.
 */
class AmazonSesController extends ControllerBase {

  use StringTranslationTrait;

  /**
   * The Guzzle HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The S3 Client.
   *
   * @var \Aws\S3\S3ClientInterface
   */
  protected $s3;

  /**
   * The logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Constructs an AmazonSesController object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The Guzzle HTTP client.
   * @param \Aws\S3\S3ClientInterface $s3_client
   *   The S3 Client.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The logger service.
   */
  public function __construct(ClientInterface $http_client, S3ClientInterface $s3_client, LoggerChannelInterface $logger) {
    $this->httpClient = $http_client;
    $this->s3 = $s3_client;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('mailgroup_amazonses.s3_client'),
      $container->get('logger.channel.mailgroup_amazonses')
    );
  }

  /**
   * Respond to POST requests.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   An empty response.
   */
  public function receive() {
    $message = SnsMessage::fromRawPostData();
    $validator = new MessageValidator();
    if ($validator->isValid($message)) {
      if ($message['Type'] == 'Notification') {
        $message_data = json_decode($message['Message']);
        $recipients = $message_data->mail->destination;

        foreach ($recipients as $recipient) {
          $group = MailGroup::loadByEmail($recipient);
          if ($group) {
            $message_id = $message_data->mail->messageId;
            $this->createMessage($group, $message_id);
          }
        }
      }
      elseif ($message['Type'] == 'SubscriptionConfirmation') {
        $this->confirm($message['SubscribeURL']);
      }
    }

    return new Response();
  }

  /**
   * Create a Mail Group Message.
   *
   * @param \Drupal\mailgroup\Entity\MailGroup $group
   *   The group to create the message in.
   * @param string $message_id
   *   The ID of the received message.
   */
  protected function createMessage(MailGroup $group, $message_id) {
    $plugin = $group->getConnectionPlugin();
    if ($plugin instanceof AmazonSes) {
      $config = $plugin->getConfig();

      try {
        /** @var \Aws\Result $result */
        $result = $this->s3->getObject([
          'Bucket' => $config['bucket'],
          'Key' => $message_id,
        ]);
        $body = $result->get('Body');
        $mail = Message::from($body);

        $values = [
          'gid' => $group->id(),
          'sender' => $mail->getHeader('From')->getEmail(),
          'subject' => $mail->getHeader('Subject')->getValue(),
          'body' => $mail->getTextContent(),
        ];
        $message = MailGroupMessage::create($values);

        try {
          $message->save();

          if ($config['delete']) {
            $this->s3->deleteMatchingObjects($config['bucket'], $message_id);
          }
        }
        catch (EntityStorageException $e) {
          $this->logger->error($e->getMessage());
          $this->logger->error($this->t('Unable to save message to @group', ['@group' => $group->getName()]));
        }
      }
      catch (S3Exception $e) {
        $this->logger->error($e->getMessage());
        $this->logger->error($this->t('Unable to create the message.'));
      }
    }
  }

  /**
   * Confirm a subscription to a SNS topic.
   *
   * @param string $subscribe_url
   *   The subscribe URL to send a request to.
   */
  protected function confirm($subscribe_url) {
    try {
      $response = $this->httpClient->request('GET', $subscribe_url);
      $code = $response->getStatusCode();

      if ($code == 200) {
        $this->logger->info($this->t('Subscription to SNS confirmed.'));
      }
      else {
        $this->logger->error($this->t('Unable to confirm subscription to SNS.'));
      }
    }
    catch (GuzzleException $e) {
      $this->logger->error($this->t('Unable to confirm subscription to SNS.'));
    }

  }

}
