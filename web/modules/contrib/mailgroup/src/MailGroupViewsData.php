<?php

namespace Drupal\mailgroup;

use Drupal\views\EntityViewsData;

/**
 * Provides views data for mail groups.
 */
class MailGroupViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['mailgroup']['membership'] = [
      'relationship' => [
        'id' => 'standard',
        'title' => $this->t('Membership'),
        'label' => $this->t('Membership'),
        'group' => $this->t('Mail group'),
        'help' => $this->t('Relate memberships to the parent group.'),
        'base' => 'mailgroup_membership',
        'base field' => 'gid',
        'relationship field' => 'id',
      ],
    ];

    $data['mailgroup']['message'] = [
      'relationship' => [
        'id' => 'standard',
        'title' => $this->t('Message'),
        'label' => $this->t('Message'),
        'group' => $this->t('Mail group'),
        'help' => $this->t('Relate messages to the parent group.'),
        'base' => 'mailgroup_message',
        'base field' => 'gid',
        'relationship field' => 'id',
      ],
    ];

    return $data;
  }

}
