<?php

namespace Drupal\config_reference_formatters\Repository;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class VoteResultRepository.
 *
 * @package Drupal\config_reference_formatters\Repository
 */
class VoteResultRepository implements VoteResultRepositoryInterface {

  /**
   * Active database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * VoteResultRepository constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection to be used.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(Connection $database, EntityTypeManagerInterface $entity_type_manager) {
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function sortEntities(array $entities, $vote_type, $vote_result_function, $order_direction = 'DESC') {
    $sorted_entities = [];

    /*
     * An associative array:
     * - key: The entity type ID.
     * - value: An associative array:
     *   - key: The entity identifier.
     *   - value: The entity delta in $entities.
     */
    $entity_index = [];
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    foreach ($entities as $entity_delta => $entity) {
      $entity_index[$entity->getEntityTypeId()][$entity->id()] = $entity_delta;
    }

    /** @var \Drupal\Core\Database\Query\SelectInterface $query */
    $query = $this->database->select('votingapi_result', 'vr')
      ->fields('vr', ['entity_type', 'entity_id', 'value'])
      ->condition('type', $vote_type)
      ->condition('function', $vote_result_function)
      ->orderBy('value', $order_direction);

    if (!empty($entity_index)) {
      $conditions = $query->orConditionGroup();
      foreach ($entity_index as $entity_type => $entity_ids) {
        $conditions->condition($query->andConditionGroup()
          ->condition('entity_type', $entity_type)
          ->condition('entity_id', array_keys($entity_ids), 'IN'));
      }
      $query->condition($conditions);
    }

    $results = $query->execute();
    while ($result = $results->fetchAssoc()) {
      $entity_delta = $entity_index[$result['entity_type']][$result['entity_id']];

      $sorted_entities[$entity_delta] = $entities[$entity_delta];
      unset($entities[$entity_delta]);
    }
    $sorted_entities += $entities;

    return $sorted_entities;
  }

}
