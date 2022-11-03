<?php

namespace Drupal\events_module\Controllers;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

class EventsModuleController extends ControllerBase
{
  public function listing()
  {

    $connection = Database::getConnection();
    $query = $connection->select('events', 'e');
    $query->fields('e', ['id', 'title', 'category', 'description', 'start_date', 'end_date', 'image']);
    $results = $query->execute()->fetchAll();
    $events = $this->events_mapper($results);


    $header = [
      'id' => t('ID'),
      'title' => t('Title'),
      'category' => t('Category'),
      'description' => t('Description'),
      'start_date' => t('Start Date'),
      'end_date' => t('End Date'),
      'image' => t('Image'),
      'edit' => t('Edit'),
      'delete' => t('Delete'),
    ];

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $events,
      '#empty' => t('No events found'),
      '#caption' => Link::createFromRoute('Add Event', 'events_module.add'),
    ];

    return $build;


  }

  public function events()
  {
    $connection = Database::getConnection();
    $query = $connection->select('events', 'e');
    $query->fields('e', ['id', 'title', 'category', 'description', 'start_date', 'end_date', 'image']);
    $results = $query->execute()->fetchAll();

    $events[] = [];
    foreach ($results as $result) {
      $events[] = $this->eventCard($result);
    }

    return [
      '#theme' => 'events',
      '#events' => $events,
      '#title' => 'Events',
    ];

  }

  public function event_details($id)
  {
    $connection = Database::getConnection();
    $query = $connection->select('events', 'e');
    $query->fields('e', ['id', 'title', 'category', 'description', 'start_date', 'end_date', 'image']);
    $query->condition('id', $id);
    $result = $query->execute()->fetchAssoc();

    $event = [
      'id' => $result['id'],
      'title' => $result['title'],
      'category' => $result['category'],
      'description' => $result['description'],
      'start_date' => $result['start_date'],
      'end_date' => $result['end_date'],
      'image' =>  file_create_url(File::load($result['image'])->getFileUri()),
    ];

    return [
      '#theme' => 'event_details',
      '#event' => $event,
      '#title' => 'Event Details',
    ];
  }


  public function events_mapper($results)
  {
    $events[] = [];
    foreach ($results as $result) {
      $events[] = [
        'id' => $result->id,
        'title' => $result->title,
        'category' => $result->category,
        'description' => $result->description,
        'start_date' => $result->start_date,
        'end_date' => $result->end_date,
        'image' => new FormattableMarkup('<img src="@path" width="100" height="100" />', ['@path' => file_create_url(File::load($result->image)->getFileUri())]),
        'edit' => Link::fromTextAndUrl('Edit', Url::fromRoute('events_module.edit', ['id' => $result->id])),
        'delete' => Link::fromTextAndUrl('Delete', Url::fromRoute('events_module.delete', ['id' => $result->id])),
      ];
    }

    return $events;
  }

  public function eventCard($result)
  {
    $event = [
      'id' => $result->id,
      'title' => $result->title,
      'category' => $result->category,
      'description' => $result->description,
      'start_date' => $result->start_date,
      'end_date' => $result->end_date,
      'image' =>  file_create_url(File::load($result->image)->getFileUri()),
      'url' => Url::fromRoute('events_module.event_details', ['id' => $result->id]),

    ];

    return [
      '#theme' => 'event_card',
      '#event' => $event,
    ];
  }
}
