<?php

namespace Drupal\mailgroup\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the mail group type entity.
 *
 * @ConfigEntityType(
 *   id = "mailgroup_type",
 *   label = @Translation("Mail group type"),
 *   label_collection = @Translation("Mail group types"),
 *   label_singular = @Translation("group type"),
 *   label_plural = @Translation("group types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count group type",
 *     plural = "@count group types"
 *   ),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\mailgroup\MailGroupTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\mailgroup\Form\MailGroupTypeForm",
 *       "edit" = "Drupal\mailgroup\Form\MailGroupTypeForm",
 *       "delete" = "Drupal\mailgroup\Form\MailGroupTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer mail groups",
 *   bundle_of = "mailgroup",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/mailgroup/types/{mailgroup_type}",
 *     "add-form" = "/admin/mailgroup/types/add",
 *     "edit-form" = "/admin/mailgroup/types/{mailgroup_type}/edit",
 *     "delete-form" = "/admin/mailgroup/types/{mailgroup_type}/delete",
 *     "collection" = "/admin/mailgroup/types"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *     "description"
 *   }
 * )
 */
class MailGroupType extends ConfigEntityBundleBase implements MailGroupTypeInterface {

  /**
   * The mail group type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The mail group type name.
   *
   * @var string
   */
  protected $label;

  /**
   * The description of the mail group type.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

}
