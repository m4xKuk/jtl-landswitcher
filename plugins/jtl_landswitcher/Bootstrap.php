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

    $service = new Service($this->getDB(), $this->lang);

    if ($tabName == 'Main' && Shop::isAdmin(true)) {

      $cities = $service->getCities();
      $objects = $service->getMainObjects();


      if (($url = Request::postVar('redirect_url')) && ($cISO = Request::postVar('cities')) && Request::postVar('add') == 1 && Form::validateToken()) {

        if (!empty($city = $service->getCity($cISO)) && !in_array($city->cISO, array_column($objects, 'cISO'))) {
          
          $url_esc = $service->escUrl($url);
          if(mb_strlen($url_esc) > 0) {
            $service->createRow($city->cISO, $url_esc);
            $objects = $service->getMainObjects();
          }else{
            $alert->addAlert(Alert::TYPE_ERROR, \__('Url error'), 'Error');
          }
          
        } else {
          $alert->addAlert(Alert::TYPE_ERROR, \__('Redirect already exist'), 'Error');
        }
      }

      if (Request::isAjaxRequest() && ($id = Request::postVar('id')) && Request::postVar('action') == 'delete') {
        
        $result = $service->deleteRow($id);

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
        $service->editRow($id, $url, $alert);
        $objects = $service->getMainObjects();
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

}
