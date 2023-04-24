<?php

declare(strict_types=1);

namespace Plugin\jtl_landswitcher;

use JTL\Alert\Alert;
use JTL\Helpers\Text;
use JTL\Shop;
use Plugin\jtl_landswitcher\models\ModelRed;

class Service
{
  /**
   * @var \JTL\DB\DbInterface
   */
  private $db;

  private $lang;

  public function __construct($db, $lang)
  {
    $this->db = $db;
    $this->lang = $lang;
  }

  public function getCities()
  {
    return
      $this->db->getObjects("
      SELECT
        cISO, $this->lang as name
        FROM tland
    ");
  }

  public function getMainObjects()
  {
    return $this->db->getArrays("
      SELECT
        tland.cISO, tr.url, tland.$this->lang as name, tr.id 
        FROM jtl_test_redirect as tr 
        INNER JOIN tland 
        ON tland.cISO = tr.tland_cISO
    ");
  }

  public function getCity($cISO)
  {
    return $this->db->getSingleObject(
      "
      SELECT *
        FROM tland 
        WHERE cISO = :cISO",
      ['cISO' => $cISO]
    );
  }

  public function escUrl($url)
  {
    return ($this->db->escape(Text::htmlspecialchars(Text::filterURL(strip_tags(trim($url))))));
  }

  public function createRow($cISO, $url)
  {
    ModelRed::create([
      'tland_cISO' => $cISO,
      'url' => $url,
      'date' => date('Y-m-d H:i:s'),
    ], $this->db);
  }

  public function deleteRow($id)
  {
    $model = ModelRed::load(['id' => $id], $this->db);
    if ($model) {
      return $model->delete();
    }
  }

  public function editRow($id, $url, $alert)
  {
    try {
      $model = ModelRed::load(['id' => $id], $this->db);
      $url_esc = $this->escUrl($url);
      if ($model && mb_strlen($url_esc) > 0) {
        $model->setUrl($url);
        $model->setDate(date('Y-m-d H:i:s'));
        $model->save();
        $alert->addAlert(Alert::TYPE_SUCCESS, \__('Updated'), 'Error');
      } else {
        $alert->addAlert(Alert::TYPE_ERROR, \__('Update error'), 'Error');
      }
    } catch (\Exception $e) {
      $alert->addAlert(Alert::TYPE_ERROR, $e->getMessage(), 'Error');
    }
  }
  
}