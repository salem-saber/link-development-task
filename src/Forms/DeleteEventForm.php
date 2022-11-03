<?php

namespace Drupal\events_module\Forms;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;


class DeleteEventForm extends ConfirmFormBase {


  /**
   * @var mixed|null
   */
  private mixed $id;

  public function getQuestion()
  {
    return $this->t('Are you sure you want to delete this event?');
  }

  public function getDescription()
  {
    return $this->t('This action cannot be undone.');
  }

  public function getConfirmText()
  {
    return $this->t('Delete');
  }


  public function getCancelUrl()
  {
    return new Url('events_module.listing');
  }

  public function getCancelText()
  {
    return $this->t('Cancel');
  }


  public function getFormId()
  {
    return 'delete_event_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL)
  {
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }


  public function submitForm(array &$form, FormStateInterface $form_state , $id = NULL)
  {
    $connection = \Drupal::database();
    $query = $connection->delete('events');
    $query->condition('id', $this->id);
    $query->execute();

    $messenger = \Drupal::messenger();
    $messenger->addMessage('Event deleted successfully');

    $form_state->setRedirect('events_module.listing');
  }

}
