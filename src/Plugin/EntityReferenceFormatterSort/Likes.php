<?php

namespace Drupal\config_reference_formatters\Plugin\EntityReferenceFormatterSort;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\config_reference_formatters\Repository\VoteResultRepositoryInterface;
use Drupal\config_reference_formatters\Plugin\EntityReferenceFormatterSortPluginBase;

/**
 * Class Likes.
 *
 * @package Drupal\config_reference_formatters\Plugin\EntityReferenceFormatterSort
 *
 * @EntityReferenceFormatterSort(
 *   id = "entity_reference_formatter_sort_likes",
 *   label = @Translation("Likes")
 * )
 */
class Likes extends EntityReferenceFormatterSortPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The vote result repository service.
   *
   * @var \Drupal\config_reference_formatters\Repository\VoteResultRepositoryInterface
   */
  protected $voteResultRepository;

  /**
   * The like vote type.
   *
   * @var string
   *
   * @see like_and_dislike/config/install/votingapi.vote_type.like.yml
   */
  protected $voteType = 'like';

  /**
   * The COUNT vote result function.
   *
   * @var string
   *
   * @see \Drupal\votingapi\Plugin\VoteResultFunction\Count
   */
  protected $voteResultFunction = 'vote_count';

  /**
   * Likes constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\config_reference_formatters\Repository\VoteResultRepositoryInterface $vote_result_repository
   *   The vote result repository service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, VoteResultRepositoryInterface $vote_result_repository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->voteResultRepository = $vote_result_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config_reference_formatters.repository.vote_result')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function sortEntities(array &$entities) {
    $entities = $this->voteResultRepository->sortEntities($entities, $this->voteType, $this->voteResultFunction);
  }

}
