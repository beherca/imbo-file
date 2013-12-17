<?php
/**
 * This file is part of the Imbo package
 *
 * (c) Christer Edvartsen <cogo@starzinger.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was
 * distributed with this source code.
 */

namespace Imbo\Http\Response\Formatter;

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
  public function formatImages(Model\Images $model) {
    $images = '';
  
    if ($fields = $model->getFields()) {
      $fields = array_fill_keys($fields, 1);
    }
  
    foreach ($model->getImages() as $image) {
      $images .= '<image>';
  
      if (empty($fields) || isset($fields['publicKey'])) {
        $images .= '<publicKey>' . $image->getPublicKey() . '</publicKey>';
      }
  
      if (empty($fields) || isset($fields['imageIdentifier'])) {
        $images .= '<imageIdentifier>' . $image->getImageIdentifier() . '</imageIdentifier>';
      }
  
      if (empty($fields) || isset($fields['checksum'])) {
        $images .= '<checksum>' . $image->getChecksum() . '</checksum>';
      }
  
      if (empty($fields) || isset($fields['mime'])) {
        $images .= '<mime>' . $image->getMimeType() . '</mime>';
      }
  
      if (empty($fields) || isset($fields['extension'])) {
        $images .= '<extension>' . $image->getExtension() . '</extension>';
      }
  
      if (empty($fields) || isset($fields['added'])) {
        $images .= '<added>' . $this->dateFormatter->formatDate($image->getAddedDate()) . '</added>';
      }
  
      if (empty($fields) || isset($fields['updated'])) {
        $images .= '<updated>' . $this->dateFormatter->formatDate($image->getUpdatedDate()) . '</updated>';
      }
  
      if (empty($fields) || isset($fields['size'])) {
        $images .= '<size>' . $image->getFilesize() . '</size>';
      }
  
      if (empty($fields) || isset($fields['width'])) {
        $images .= '<width>' . $image->getWidth() . '</width>';
      }
  
      if (empty($fields) || isset($fields['height'])) {
        $images .= '<height>' . $image->getHeight() . '</height>';
      }
  
      $metadata = $image->getMetadata();
  
      if (is_array($metadata) && (empty($fields) || isset($fields['metadata']))) {
        $images .= '<metadata>';
  
        foreach ($metadata as $key => $value) {
          $images .= '<tag key="' . $key . '">' . $value . '</tag>';
        }
  
        $images .= '</metadata>';
      }
  
      $images .= '</image>';
    }
  
    return <<<IMAGES
<?xml version="1.0" encoding="UTF-8"?>
<imbo>
  <images>{$images}</images>
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
