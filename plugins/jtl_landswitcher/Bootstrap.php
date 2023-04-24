<?php

declare(strict_types=1);

namespace Plugin\jtl_landswitcher;

use JTL\Alert\Alert;
use JTL\Plugin\Bootstrapper;
use JTL\Smarty\JTLSmarty;
use JTL\Events\Dispatcher;
use JTL\Helpers\Form;
use JTL\Shop;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use Plugin\jtl_landswitcher\models\ModelRed;

/**
 * Class Bootstrap
 * @package Plugin\jtl_landswitcher
 */
class Bootstrap extends Bootstrapper
{
  public $lang;

  /**
   * @inheritdoc
   */
  public function renderAdminMenuTab(string $tabName, int $menuID, JTLSmarty $smarty): string
  {
    $plugin   = $this->getPlugin();
    $template = 'add.tpl';
    $this->lang = (mb_convert_case(Shop::getLanguageCode(), MB_CASE_LOWER) === 'ger') ? 'cDeutsch' : 'cEnglisch';
    $alert = Shop::Container()->getAlertService();

    if ($tabName == 'Main' && Shop::isAdmin(true)) {

      $cities = $this->getCities();
      $objects = $this->getMainObjects();


      if (($url = Request::postVar('redirect_url')) && ($cISO = Request::postVar('cities')) && Request::postVar('add') == 1 && Form::validateToken()) {

        if (!empty($city = $this->getCity($cISO)) && !in_array($city->cISO, array_column($objects, 'cISO'))) {
          
          $url_esc = $this->escUrl($url);
          if(mb_strlen($url_esc) > 0) {
            ModelRed::create([
              'tland_cISO' => $city->cISO,
              'url' => $url_esc,
            ], $this->getDB());
            $objects = $this->getMainObjects();
          }else{
            $alert->addAlert(Alert::TYPE_ERROR, \__('Url error'), 'Error');
          }
          
        } else {
          $alert->addAlert(Alert::TYPE_ERROR, \__('Redirect already exist'), 'Error');
        }
      }

      if (Request::isAjaxRequest() && ($id = Request::postVar('id')) && Request::postVar('action') == 'delete') {
        $model = ModelRed::load(['id' => $id], $this->getDB());
        if ($model) {
          $result = $model->delete();
        }

        if (isset($result)) {
          $message = [
            'status' => 'success',
            'message' => 'Deleted'
          ];
          echo json_encode($message);;
        }
        exit;
      }

      if (Form::validateToken() && ($id = Request::postVar('id')) && ($url = Request::postVar('redirect_url')) && Request::postVar('edit') == 'edit') {
        try{
          $model = ModelRed::load(['id' => $id], $this->getDB());
          $url_esc = $this->escUrl($url);
          if ($model && mb_strlen($url_esc) > 0) {
            $model->setUrl($url);
            $model->save();
            $objects = $this->getMainObjects();
            $alert->addAlert(Alert::TYPE_SUCCESS, \__('Updated'), 'Error');
          } else {
            $alert->addAlert(Alert::TYPE_ERROR, \__('Update error'), 'Error');
          }
        } catch (\Exception $e) {
          $alert->addAlert(Alert::TYPE_ERROR, $e->getMessage(), 'Error');
        }


      }

      return $smarty->assign(
        'adminURL',
        Shop::getAdminURL() . '/plugin.php?kPlugin=' . $plugin->getID()
      )
        ->assign('objects', $objects)
        ->assign('cities', $cities)
        ->fetch($this->getPlugin()->getPaths()->getAdminPath() . '/templates/' . $template);
    }
  }

  /**
   * @inheritdoc
   */
  public function boot(Dispatcher $dispatcher)
  {
    parent::boot($dispatcher);
  }

  private function getCities()
  {
    return
      $this->getDB()->getObjects("
      SELECT
        cISO, $this->lang as name
        FROM tland
    ");
  }

  private function getMainObjects()
  {
    return $this->getDB()->getArrays("
      SELECT
        tland.cISO, tr.url, tland.$this->lang as name, tr.id 
        FROM jtl_test_redirect as tr 
        INNER JOIN tland 
        ON tland.cISO = tr.tland_cISO
    ");
  }

  private function getCity($cISO)
  {
    return $this->getDB()->getSingleObject("
      SELECT *
        FROM tland 
        WHERE cISO = :cISO",
      ['cISO' => $cISO]
    );
  }

  private function escUrl($url)
  {
    return ($this->getDB()->escape(Text::htmlspecialchars(Text::filterURL(strip_tags(trim($url))))));
  }
}
