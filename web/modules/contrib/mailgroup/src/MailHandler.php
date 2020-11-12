<?php

namespace Drupal\mailgroup;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\mailgroup\Entity\MailGroupMessageInterface;

/**
 * Serivce for handling mail group mail.
 */
class MailHandler {
  use StringTranslationTrait;

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Constructs the service.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The logger channel.
   */
  public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager, LoggerChannelInterface $logger) {
    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
    $this->logger = $logger;
  }

  /**
   * Send a message to members of the group.
   *
   * @param \Drupal\mailgroup\Entity\MailGroupMessageInterface $message
   *   The message to send.
   */
  public function sendMessage(MailGroupMessageInterface $message) {
    /** @var \Drupal\mailgroup\Entity\MailGroup $group */
    $group = $message->getGroup();

    $group_name = $group->getName();
    $group_email = $group->getEmail();

    $to = 'undisclosed-recipients:;';
    $emails = $group->getMemberEmails();

    $reply_to = $group->getConfig('reply_to');
    if ($reply_to == 'all') {
      $reply = $group_email;
    }
    elseif ($reply_to == 'sender') {
      $reply = $message->getSender();
    }

    $langcode = $this->languageManager->getCurrentLanguage()->getId();

    $params = [
      'subject' => $message->getSubject(),
      'message' => $message->getBody(),
      'emails' => implode(', ', $emails),
      'from' => "$group_name <$group_email>",
    ];

    $result = $this->mailManager->mail('mailgroup', 'group_send', $to, $langcode, $params, $reply);
    if ($result['result']) {
      $log_message = $this->t('Message sent to @group.', ['@group' => $group_name]);
      $this->logger->notice($log_message);
    }
    else {
      $log_message = $this->t('There was a problem sending a message to @email.', [
        '@email' => $to,
      ]);
      $this->logger->error($log_message);
    }
  }

}
