<?php

namespace Drupal\lending_library_borrower_role\Plugin\WebformHandler;

use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormStateInterface;

/**
 * Webform handler to assign the borrower role upon form submission.
 *
 * @WebformHandler(
 *   id = "lending_library_borrower_role_handler",
 *   label = @Translation("Lending Library Borrower Role"),
 *   category = @Translation("User"),
 *   description = @Translation("Assigns the borrower role when a specific webform is submitted."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class LendingLibraryBorrowerRoleHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = FALSE) {
    $user = \Drupal::currentUser();

    if ($user->isAnonymous()) {
      \Drupal::messenger()->addMessage(t('Only logged-in users can be assigned the borrower role.'), 'error');
      return;
    }

    // Load the full user entity.
    $user_entity = User::load($user->id());

    if ($user_entity) {
      if (!$user_entity->hasRole('borrower')) {
        $user_entity->addRole('borrower');
        $user_entity->save();
        \Drupal::messenger()->addMessage(t('You have been assigned the borrower role.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [];
  }
}
