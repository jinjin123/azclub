<?php

namespace Drupal\default_content_deploy;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Session\AccountSwitcherInterface;
use Drupal\hal\LinkManager\LinkManagerInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * A service for handling export of default content.
 */
class Exporter {

  /**
   * DCD Manager.
   *
   * @var \Drupal\default_content_deploy\DeployManager
   */
  protected $deployManager;

  /**
   * Default content Exporter.
   *
   * @var \Drupal\default_content\Exporter
   */
  protected $exporter;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * DB connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Serializer.
   *
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $serializer;

  /**
   * The account switcher service.
   *
   * @var \Drupal\Core\Session\AccountSwitcherInterface
   */
  protected $accountSwitcher;

  /**
   * Entity type ID.
   *
   * @var string
   */
  private $entityTypeId;

  /**
   * Type of a entity content.
   *
   * @var string
   */
  private $bundle;

  /**
   * Entity IDs for export.
   *
   * @var array
   */
  private $entityIds;

  /**
   * Directory to export.
   *
   * @var string
   */
  private $folder;

  /**
   * Entity IDs which needs skip.
   *
   * @var array
   */
  private $skipEntityIds;

  /**
   * Array of entity types and with there values for export.
   *
   * @var array
   */
  private $exportedEntities = [];

  /**
   * Type of export.
   *
   * @var string
   */
  private $mode;

  /**
   * Is remove old content.
   *
   * @var bool
   */
  private $forceUpdate;

  /**
   * The link manager service.
   *
   * @var \Drupal\hal\LinkManager\LinkManagerInterface
   */
  protected $linkManager;

  /**
   * Exporter constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   DB connection.
   * @param \Drupal\default_content_deploy\DeployManager $deploy_manager
   *   DCD Manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity Type Manager.
   * @param \Symfony\Component\Serializer\Serializer $serializer
   *   Serializer.
   * @param \Drupal\Core\Session\AccountSwitcherInterface $account_switcher
   *   The account switcher service.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Drupal\hal\LinkManager\LinkManagerInterface $link_manager
   *   The link manager service.
   */
  public function __construct(Connection $database, DeployManager $deploy_manager, EntityTypeManagerInterface $entityTypeManager, Serializer $serializer, AccountSwitcherInterface $account_switcher, FileSystemInterface $file_system, LinkManagerInterface $link_manager) {
    $this->database = $database;
    $this->entityTypeManager = $entityTypeManager;
    $this->serializer = $serializer;
    $this->accountSwitcher = $account_switcher;
    $this->deployManager = $deploy_manager;
    $this->fileSystem = $file_system;
    $this->linkManager = $link_manager;
  }

  /**
   * Set entity type ID.
   *
   * @param string $entity_type
   *   Entity Type.
   *
   * @return \Drupal\default_content_deploy\Exporter
   */
  public function setEntityTypeId($entity_type) {
    $content_entity_types = $this->deployManager->getContentEntityTypes();

    if (!array_key_exists($entity_type, $content_entity_types)) {
      throw new \InvalidArgumentException(sprintf('Entity type "%s" does not exist', $entity_type));
    }

    $this->entityTypeId = (string) $entity_type;

    return $this;
  }

  /**
   * Set type of a entity content.
   *
   * @param string $bundle
   *  Bundle of the entity type.
   *
   * @return \Drupal\default_content_deploy\Exporter
   */
  public function setEntityBundle($bundle) {
    $this->bundle = $bundle;
    return $this;
  }

  /**
   * Set entity IDs for export.
   *
   * @param array $entity_ids
   *   The IDs of entity.
   *
   * @return \Drupal\default_content_deploy\Exporter
   */
  public function setEntityIds(array $entity_ids) {
    $this->entityIds = $entity_ids;
    return $this;
  }

  /**
   * Set entity IDs which needs skip.
   *
   * @param array $skip_entity_ids
   *   The IDs of entity for skip.
   *
   * @return $this
   */
  public function setSkipEntityIds(array $skip_entity_ids) {
    $this->skipEntityIds = $skip_entity_ids;
    return $this;
  }

  /**
   * Set type of export.
   *
   * @param string $mode
   *  Value type of export.
   *
   * @return \Drupal\default_content_deploy\Exporter
   *
   * @throws \Exception
   */
  public function setMode($mode) {
    $available_modes = ['all', 'reference', 'default'];

    if (in_array($mode, $available_modes)) {
      $this->mode = $mode;
    }
    else {
      throw new \Exception('The selected mode is not available');
    }

    return $this;
  }

  /**
   * Is remove old content.
   *
   * @param bool $is_update
   *
   * @return \Drupal\default_content_deploy\Exporter
   */
  public function setForceUpdate(bool $is_update) {
    $this->forceUpdate = $is_update;
    return $this;
  }

  /**
   * Set directory to export.
   *
   * @param string $folder
   *   The content folder.
   *
   * @return \Drupal\default_content_deploy\Exporter
   */
  public function setFolder($folder) {
    $this->folder = $folder;
    return $this;
  }

  /**
   * Get directory to export.
   *
   * @return string
   *   The content folder.
   *
   * @throws \Exception
   */
  protected function getFolder() {
    $folder = $this->folder ?: $this->deployManager->getContentFolder();
    return $folder;
  }

  /**
   * Array with entity types for display result.
   *
   * @return array
   *   Array with entity types.
   */
  public function getResult() {
    return $this->exportedEntities;
  }

  /**
   * Export entities by entity type, id or bundle.
   *
   * @return \Drupal\default_content_deploy\Exporter
   *
   * @throws \Exception
   */
  public function export() {
    switch ($this->mode) {
      case 'default':
        $this->prepareToExport();
        break;

      case 'reference':
        $this->prepareToExportWithReference();
        break;

      case 'all':
        $this->prepareToExportAllContent();
        break;
    }

    // Edit and export all entities to folder.
    $this->editEntityData();
    $this->writeConfigsToFolder();

    return $this;
  }

  /**
   * Prepare content to export.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function prepareToExport() {
    $entity_type = $this->entityTypeId;
    $exported_entity_ids = $this->getEntityIdsForExport();

    if ($this->forceUpdate) {
      $this->fileSystem->deleteRecursive($this->getFolder());
    }

    foreach ($exported_entity_ids as $entity_id) {
      /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
      $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);
      $exported_entity = $this->getSerializedContent($entity);
      $this->addExportedEntity($exported_entity);
    }
  }

  /**
   * Prepare content with reference to export.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function prepareToExportWithReference() {
    $entity_type = $this->entityTypeId;
    $exported_entity_ids = $this->getEntityIdsForExport();

    if ($this->forceUpdate) {
      $this->fileSystem->deleteRecursive($this->getFolder());
    }

    foreach ($exported_entity_ids as $entity_id) {
      /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
      $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);
      $exported_entity = $this->getSerializedContentWithReferences($entity);
      $this->addExportedEntity($exported_entity);
    }
  }

  /**
   * Prepare all content on the site to export.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function prepareToExportAllContent() {
    $content_entity_types = $this->deployManager->getContentEntityTypes();

    if ($this->forceUpdate) {
      $this->fileSystem->deleteRecursive($this->getFolder());
    }

    foreach ($content_entity_types as $entity_type => $label) {
      // Skip specified entities in --skip_entity_type option.
      if (!$this->skipEntityIds || !in_array($entity_type, $this->skipEntityIds)) {
        $this->setEntityTypeId($entity_type);
        $query = $this->entityTypeManager->getStorage($entity_type)->getQuery();
        $entity_ids = array_values($query->execute());

        foreach ($entity_ids as $entity_id) {
          /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
          $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);
          $exported_entity = $this->getSerializedContent($entity);
          $this->addExportedEntity($exported_entity);
        }
      }
    }
  }

  /**
   * Get all entity IDs for export.
   *
   * @return array
   *   Return array of entity ids.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function getEntityIdsForExport() {
    $skip_entities = $this->skipEntityIds;
    $entity_ids = $this->entityIds;
    $entity_type = $this->entityTypeId;
    $entity_bundle = $this->bundle;
    $key_bundle = $this->entityTypeManager->getDefinition($entity_type)->getKey('bundle');

    // If the Entity IDs option is null then load all IDs.
    if (empty($entity_ids)) {
      $query = $this->entityTypeManager->getStorage($entity_type)->getQuery();

      if ($entity_bundle) {
        $query->condition($key_bundle, $entity_bundle);
      }

      $entity_ids = $query->execute();
    }

    // Remove skipped entities from $exported_entity_ids.
    if (!empty($skip_entities)) {
      $entity_ids = array_diff($entity_ids, $skip_entities);
    }

    return $entity_ids;
  }

  /**
   * Add array with entity info for export.
   *
   * @param $exported_entity
   *   Entity info.
   *
   * @return $this
   */
  private function addExportedEntity($exported_entity) {
    if (is_string($exported_entity)) {
      $entity = $this->serializer->decode($exported_entity, 'hal_json');
      $uuid = $entity['uuid'][0]['value'];
      $entity_with_uuid[$uuid] = $exported_entity;

      $exported_entity_array[$this->entityTypeId][$uuid] = $exported_entity;
    }
    else {
      $exported_entity_array = $exported_entity;
    }

    $this->exportedEntities = array_replace_recursive($this->exportedEntities, $exported_entity_array);

    return $this;
  }

  /**
   * Writes an array of serialized entities to a given folder.
   *
   * @return $this
   *
   * @throws \Exception
   */
  private function writeConfigsToFolder() {
    foreach ($this->exportedEntities as $entity_type => $serialized_entities) {
      // Ensure that the folder per entity type exists.
      $entity_type_folder = "{$this->getFolder()}/{$entity_type}";
      $this->fileSystem->prepareDirectory($entity_type_folder, FileSystemInterface::CREATE_DIRECTORY);

      foreach ($serialized_entities as $uuid => $serialized_entity) {
        file_put_contents("{$entity_type_folder}/{$uuid}.json", $serialized_entity);
      }
    }

    return $this;
  }

  /**
   * Remove or add a new fields to serialize entities data.
   */
  private function editEntityData() {
    foreach ($this->exportedEntities as $entity_type => $uuids) {
      foreach ($uuids as $uuid => $serialisation_entity) {
        $entity_array = $this->serializer->decode($serialisation_entity, 'hal_json');
        $entity_type_object = $this->entityTypeManager->getDefinition($entity_type);
        $id_key = $entity_type_object->getKey('id');
        $entity_id = $entity_array[$id_key][0]['value'];
        $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);

        // Removed data.
        unset($entity_array[$id_key]);
        unset($entity_array[$entity_type_object->getKey('revision')]);

        // Add data.
        if ($entity_type == 'user') {
          $entity_array['pass'][0]['value'] = $entity->getPassword();
        }

        $data = $this->serializer->serialize($entity_array, 'hal_json', [
          'json_encode_options' => JSON_PRETTY_PRINT
        ]);

        $this->exportedEntities[$entity_type][$uuid] = $data;
      }
    }
  }

  /**
   * Exports a single entity as importContent expects it.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @return string
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getSerializedContent(ContentEntityInterface $entity) {
    if (PHP_SAPI === 'cli') {
      $root_user = $this->entityTypeManager->getStorage('user')->load(1);
      $this->accountSwitcher->switchTo($root_user);
    }

    $host = $this->deployManager->getCurrentHost();
    $this->linkManager->setLinkDomain($host);
    $content = $this->serializer->serialize($entity, 'hal_json', ['json_encode_options' => JSON_PRETTY_PRINT]);
    $this->linkManager->setLinkDomain(FALSE);

    if (PHP_SAPI === 'cli') {
      $this->accountSwitcher->switchBack();
    }

    return $content;
  }

  /**
   * Exports a single entity and all its referenced entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @return array
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function getSerializedContentWithReferences(ContentEntityInterface $entity) {
    $indexed_dependencies = [$entity->uuid() => $entity];
    $entities = $this->getEntityReferencesRecursive($entity, 0, $indexed_dependencies);
    $host = $this->deployManager->getCurrentHost();
    $serialized_entities = [];

    $this->linkManager->setLinkDomain($host);
    if (PHP_SAPI === 'cli') {
      $root_user = $this->entityTypeManager->getStorage('user')->load(1);
      $this->accountSwitcher->switchTo($root_user);
    }

    // Serialize all entities and key them by entity TYPE and uuid.
    foreach ($entities as $entity) {
      $serialize = $this->serializer->serialize($entity, 'hal_json', [
        'json_encode_options' => JSON_PRETTY_PRINT,
      ]);

      $serialized_entities[$entity->getEntityTypeId()][$entity->uuid()] = $serialize;
    }

    // Reset the link domain and the current user, if needed.
    $this->linkManager->setLinkDomain(FALSE);
    if (PHP_SAPI === 'cli') {
      $this->accountSwitcher->switchBack();
    }

    return $serialized_entities;
  }

  /**
   * Returns all referenced entities of an entity.
   *
   * This method is also recursive to support use-cases like a node -> media
   * -> file.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity.
   * @param int $depth
   *   Guard against infinite recursion.
   * @param \Drupal\Core\Entity\ContentEntityInterface[] $indexed_dependencies
   *   Previously discovered dependencies.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface[]
   *   Keyed array of entities indexed by entity type and ID.
   */
  private function getEntityReferencesRecursive(ContentEntityInterface $entity, $depth = 0, array &$indexed_dependencies = []) {
    $entity_dependencies = $entity->referencedEntities();

    foreach ($entity_dependencies as $dependent_entity) {
      // Config entities should not be exported but rather provided by default
      // config.
      if (!($dependent_entity instanceof ContentEntityInterface)) {
        continue;
      }

      // Using UUID to keep dependencies unique to prevent recursion.
      $key = $dependent_entity->uuid();
      if (isset($indexed_dependencies[$key])) {
        // Do not add already indexed dependencies.
        continue;
      }

      $indexed_dependencies[$key] = $dependent_entity;
      // Build in some support against infinite recursion.
      if ($depth < 6) {
        $indexed_dependencies += $this->getEntityReferencesRecursive($dependent_entity, $depth + 1, $indexed_dependencies);
      }
    }

    return $indexed_dependencies;
  }

}
