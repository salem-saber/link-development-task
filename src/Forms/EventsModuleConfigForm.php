<?php

namespace Drupal\events_module\Forms;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class EventsModuleConfigForm extends FormBase
{


  public function getFormId()
  {
    return 'events_module_config_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = Drupal::config('events_module.settings');

    $form['events_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Events per page'),
      '#default_value' => $config->get('events_per_page'),
      '#required' => TRUE,
    ];

    $form['show_past_events'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show past events'),
      '#default_value' => $config->get('show_past_events'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $config = Drupal::configFactory()->getEditable('events_module.settings');
    $config->set('events_per_page', $form_state->getValue('events_per_page'));
    $config->set('show_past_events', $form_state->getValue('show_past_events'));
    $config->save();

    $messenger = \Drupal::messenger();
    $messenger->addMessage('Event edited successfully');
  }
}
