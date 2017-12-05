<?php

namespace Drupal\config_reference_formatters\Repository;

/**
 * Interface VoteResultRepositoryInterface.
 *
 * @package Drupal\config_reference_formatters\Repository
 */
interface VoteResultRepositoryInterface {

  /**
   * Sorts entities by their vote results.
   *
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   An array of entities to sort by their vote results.
   * @param string $vote_type
   *   Vote type.
   * @param string $vote_result_function
   *   Vote result function.
   * @param string $order_direction
   *   (optional) The direction to sort. Legal values are "ASC" and "DESC". Any
   *   other value will be converted to "ASC". Defaults to "DESC".
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   The array of entities sorted by their vote results.
   *
   * @see \Drupal\votingapi\Entity\VoteType
   * @see \Drupal\votingapi\VoteResultFunctionManager
   * @see \Drupal\votingapi\Entity\VoteResult
   * @see \Drupal\Core\Database\Query\SelectInterface::orderBy()
   */
  public function sortEntities(array $entities, $vote_type, $vote_result_function, $order_direction = 'DESC');

}
