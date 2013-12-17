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

use Imbo\Http\Response\Formatter\XML as ImboXML,
    ImboFile\Model;

/**
 * XML formatter
 *
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @package Response\Formatters
 */
class XML extends ImboXML {
   
  /**
   * {@inheritdoc}
   */
  public function formatFiles(Model\Files $model) {
    $files = '';
  
    if ($fields = $model->getFields()) {
      $fields = array_fill_keys($fields, 1);
    }
  
    foreach ($model->getFiles() as $file) {
      $files .= '<file>';
  
      if (empty($fields) || isset($fields['publicKey'])) {
        $files .= '<publicKey>' . $file->getPublicKey() . '</publicKey>';
      }
  
      if (empty($fields) || isset($fields['fileIdentifier'])) {
        $files .= '<fileIdentifier>' . $file->getFileIdentifier() . '</fileIdentifier>';
      }
  
      if (empty($fields) || isset($fields['checksum'])) {
        $files .= '<checksum>' . $file->getChecksum() . '</checksum>';
      }
  
      if (empty($fields) || isset($fields['mime'])) {
        $files .= '<mime>' . $file->getMimeType() . '</mime>';
      }
  
      if (empty($fields) || isset($fields['extension'])) {
        $files .= '<extension>' . $file->getExtension() . '</extension>';
      }
  
      if (empty($fields) || isset($fields['added'])) {
        $files .= '<added>' . $this->dateFormatter->formatDate($file->getAddedDate()) . '</added>';
      }
  
      if (empty($fields) || isset($fields['updated'])) {
        $files .= '<updated>' . $this->dateFormatter->formatDate($file->getUpdatedDate()) . '</updated>';
      }
  
      if (empty($fields) || isset($fields['size'])) {
        $files .= '<size>' . $file->getFilesize() . '</size>';
      }
  
      if (empty($fields) || isset($fields['width'])) {
        $files .= '<width>' . $file->getWidth() . '</width>';
      }
  
      if (empty($fields) || isset($fields['height'])) {
        $files .= '<height>' . $file->getHeight() . '</height>';
      }
  
      $metadata = $file->getMetadata();
  
      if (is_array($metadata) && (empty($fields) || isset($fields['metadata']))) {
        $files .= '<metadata>';
  
        foreach ($metadata as $key => $value) {
          $files .= '<tag key="' . $key . '">' . $value . '</tag>';
        }
  
        $files .= '</metadata>';
      }
  
      $files .= '</file>';
    }
  
    return <<<IMAGES
<?xml version="1.0" encoding="UTF-8"?>
<imbo>
  <files>{$files}</files>
</imbo>
IMAGES;
  }
  
  /**
   * {@inheritdoc}
   */
  public function format(Model\ModelInterface $model) {
    if ($model instanceof Model\File) {
      return $this->formatFile($model);
    }
    parent::format($model);
  }
}
