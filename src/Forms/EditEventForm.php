<?php

namespace Drupal\events_module\Forms;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

class  EditEventForm extends FormBase
{


  private $id;

  public function getFormId()
  {
    return 'edit_event_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $id = null)
  {

    $this->id = $id;

    $connection = \Drupal::database();
    $query = $connection->select('events', 'e');
    $query->fields('e', ['id', 'title', 'category', 'description', 'start_date', 'end_date', 'image']);
    $query->condition('id', $this->id);
    $results = $query->execute()->fetchAssoc();


    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#required' => TRUE,
      '#default_value' => $results['title'],
    ];

    $form['category'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category'),
      '#required' => TRUE,
      '#default_value' => $results['category'],
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#required' => TRUE,
      '#default_value' => $results['description'],
    ];

    $form['start_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Start Date'),
      '#required' => TRUE,
      '#default_value' => $results['start_date'],
    ];

    $form['end_date'] = [
      '#type' => 'date',
      '#title' => $this->t('End Date'),
      '#required' => TRUE,
      '#default_value' => $results['end_date'],
    ];

    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Image'),
      '#upload_location' => 'public://storage/events/',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
      ],
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('image') == NULL) {
      $form_state->setErrorByName('image', $this->t('File.'));
    }

    if ($form_state->getValue('start_date') > $form_state->getValue('end_date')) {
      $form_state->setErrorByName('start_date', $this->t('Start date must be before end date.'));
    }
  }


  public function submitForm(array &$form, FormStateInterface $form_state, $id = null)
  {

    $title = $form_state->getValue('title');
    $description = $form_state->getValue('description');
    $category = $form_state->getValue('category');
    $start_date = $form_state->getValue('start_date');
    $end_date = $form_state->getValue('end_date');
    $image = $form_state->getValue('image');

    Drupal::database()->update('events')->fields([
      'title' => $title,
      'description' => $description,
      'category' => $category,
      'start_date' => $start_date,
      'end_date' => $end_date,
      'image' => $image[0],
    ])->condition('id', $this->id)->execute();


    $messenger = \Drupal::messenger();
    $messenger->addMessage('Event edited successfully');
    $form_state->setRedirect('events_module.listing');
  }
}
