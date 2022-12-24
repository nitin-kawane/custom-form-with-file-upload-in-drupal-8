<?php

namespace Drupal\fileupload\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * Class FileuploadForm.
 *
 * @package Drupal\fileupload\Form
 */
class FileuploadForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fileupload_form';
  }

  /**
   * {@inheritdoc}
   */
	public function buildForm(array $form, FormStateInterface $form_state) {
		$form = [
      '#attributes' => [
        'enctype' => 'multipart/form-data'
      ],
    ];

		$validators = [
		  'file_validate_extensions' => ['pdf'],
    ];

		$form['my_file'] = [
			'#type' => 'managed_file',
			'#name' => 'my_file',
			'#title' => t('File *'),
			'#size' => 20,
			'#description' => t('PDF format only'),
			'#upload_validators' => $validators,
			'#upload_location' => 'public://my_files/',
    ];

		$form['employee_name'] = [
			'#type' => 'textfield',
			'#title' => t('Employee Name:'),
			'#required' => TRUE,
    ];

		$form['submit'] = [
			'#type' => 'submit',
			'#value' => 'save',
		];

		return $form;
	}

  /**
  * {@inheritdoc}
  */
	public function validateForm(array &$form, FormStateInterface $form_state) {

		$name = $form_state->getValue('employee_name');
		if(preg_match('/[^A-Za-z ]/', $name)) {
			$form_state->setErrorByName('employee_name', $this->t('Please enter valid Name'));
		}

		parent::validateForm($form, $form_state);
	}

  /**
   * {@inheritdoc}
   */
	public function submitForm(array &$form, FormStateInterface $form_state) {

		$field=$form_state->getValues();
		$fileData = $form_state->getValue('my_file');

		$newFile = File::load(reset($fileData));
		$newFile->setPermanent();
		$document_name = $newFile->getFilename();
		$employee_name=$field['employee_name'];

		$field  = [
		  'employee_name'   =>  $employee_name,
		  'document_name' =>  $document_name,
    ];
		$query = \Drupal::database();
		$query ->insert('fileupload')
			->fields($field)
			->execute();
		$this->messenger()->addStatus('Successfully saved.');

		$form_state->setRedirect('fileupload.fileupload_controller_display');
	}

}
