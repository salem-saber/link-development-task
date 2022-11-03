<?php

namespace Drupal\events_module\Forms;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class  AddEventForm extends FormBase {


  public function getFormId()
  {
    return 'add_event_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#required' => TRUE,
    ];

    $form['category'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category'),
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#required' => TRUE,
    ];

    $form['start_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Start Date'),
      '#required' => TRUE,
    ];

    $form['end_date'] = [
      '#type' => 'date',
      '#title' => $this->t('End Date'),
      '#required' => TRUE,
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
      '#value' => $this->t('Save'),
    ];

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('image') == NULL) {
      $form_state->setErrorByName('image', $this->t('File.'));
    }

    if ($form_state->getValue('start_date') > $form_state->getValue('end_date')) {
      $form_state->setErrorByName('start_date', $this->t('Start date must be before end date.'));
    }
  }


  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $title = $form_state->getValue('title');
    $description = $form_state->getValue('description');
    $category = $form_state->getValue('category');
    $start_date = $form_state->getValue('start_date');
    $end_date = $form_state->getValue('end_date');
    $image = $form_state->getValue('image');


    Drupal::database()->insert('events')
      ->fields([
        'title' => $title,
        'description' => $description,
        'category' => $category,
        'start_date'  => $start_date,
        'end_date' => $end_date,
        'image' => $image[0],
      ])->execute();


    $messenger = \Drupal::messenger();
    $messenger->addMessage('Event added successfully');
    $form_state->setRedirect('events_module.listing');
  }
}
