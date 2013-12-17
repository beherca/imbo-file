<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace ImboFile\Http\Response\Formatter;

use Imbo\Http\Response\Formatter\JSON as ImboJSON,
    Imbo\Model as ImboModel,
    ImboFile\Model\File,
    stdClass;

/**
 * JSON formatter
 *
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @package Response\Formatters
 */
class JSON extends ImboJSON{
  
  /**
   * {@inheritdoc}
   */
  public function formatFiles(Model\Files $model) {
    $files = $model->getFiles();
    $data = array();
  
    // Fields to display
    if ($fields = $model->getFields()) {
      $fields = array_fill_keys($fields, 1);
    }
  
    foreach ($files as $file) {
      $entry = array(
          'added' => $this->dateFormatter->formatDate($file->getAddedDate()),
          'updated' => $this->dateFormatter->formatDate($file->getUpdatedDate()),
          'checksum' => $file->getChecksum(),
          'extension' => $file->getExtension(),
          'size' => $file->getFilesize(),
          'mime' => $file->getMimeType(),
          'fileIdentifier' => $file->getImageIdentifier(),
          'publicKey' => $file->getPublicKey(),
      );
  
      // Add metadata if the field is to be displayed
      if (empty($fields) || isset($fields['metadata'])) {
        $metadata = $file->getMetadata();
  
        if (is_array($metadata)) {
          if (empty($metadata)) {
            $metadata = new stdClass();
          }
  
          $entry['metadata'] = $metadata;
        }
      }
  
      // Remove elements that should not be displayed
      if (!empty($fields)) {
        foreach (array_keys($entry) as $key) {
          if (!isset($fields[$key])) {
            unset($entry[$key]);
          }
        }
      }
  
      $data[] = $entry;
    }
  
    return $this->encode($data);
  }
  
  /**
   * {@inheritdoc}
   */
  public function format(ImboModel\ModelInterface $model) {
    if ($model instanceof Model\File) {
      return $this->formatFiles($model);
    }
    parent::format($model);
  }
}
