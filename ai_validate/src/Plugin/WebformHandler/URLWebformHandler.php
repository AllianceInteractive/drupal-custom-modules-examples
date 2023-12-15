<?php

namespace Drupal\ai_validate\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\Component\Utility\Html;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Webform validate handler.
 *
 * @WebformHandler(
 *   id = "ai_validate_url_validator",
 *   label = @Translation("URL validator"),
 *   category = @Translation("Settings"),
 *   description = @Translation("Block webform submissions with URLs"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class URLWebformHandler extends WebformHandlerBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $this->validateMessage($form_state);
  }

  /**
   * Validate form.
   */
  private function validateMessage(FormStateInterface $formState) {
  	$fields = $formState->cleanValues()->getValues();
  	foreach ($fields as $key => $value) {  	
	    // Skip empty unique fields or arrays (aka #multiple).
	    if (empty($value) || is_array($value)) {
	      return;
	    }
	    if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value)) {
	      $formState->setErrorByName($key, $this->t('Field value is not valid.'));
	      //Log the warning
        $message = $this->t('Webform submission with URLs blocked.');
        \Drupal::logger('ai_validate')->warning($message);           
	    }
	    else {
	      $formState->setValue($key, $value);
	    }  	
	}
  }  

}  