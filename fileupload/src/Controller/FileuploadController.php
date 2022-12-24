<?php
namespace Drupal\fileupload\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

/**
 * Class FileuploadController.
 *
 * @package Drupal\fileupload\Controller
 */

class FileuploadController extends ControllerBase {

  /**
   * Display.
   * @return string
   */
  public function display() {
    $header_table = [
      'id'=>    t('SrNo'),
      'employee_name' => t('Employee Name'),
      'document_name' => t('Document Name'),
      'action' => t('Action'),
    ];

    $query = \Drupal::database()->select('fileupload', 'f');
    $query->fields('f', ['id','employee_name','document_name']);
    $results = $query->execute()->fetchAll();
    $rows=array();
    foreach($results as $data){
      $view = Url::fromUserInput('/sites/default/files/my_files/'.$data->document_name);
      $rows[] = [
        'id' =>$data->id,
        'employee_name' => $data->employee_name,
        'document_name' => $data->document_name,
        \Drupal::l('View File', $view),
      ];
    }
    
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No users found'),
    ];
    return $form;
  }
  
}
