<?php

use Admin\Controller\AdminAppController;
use App\Controller\Component\StringComponent;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * PagesController
 *
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, http://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class PagesController extends AdminAppController
{

    public function isAuthorized($user)
    {
        switch ($this->action) {
            case 'home':
                if ($this->AppAuth->user()) {
                    return true;
                }
                break;
            default:
                return $this->AppAuth->isSuperadmin() || $this->AppAuth->isAdmin();
        }
    }

    public function home()
    {
        $this->set('title_for_layout', 'Home');
    }

    public function add()
    {
        $this->edit();
        $this->set('title_for_layout', 'Seite erstellen');
        $this->render('edit');
    }

    public function edit($pageId = null)
    {
        $this->setFormReferer();

        $_SESSION['KCFINDER'] = array(
            'uploadURL' => Configure::read('AppConfig.cakeServerName') . "/files/kcfinder/pages",
            'uploadDir' => $_SERVER['DOCUMENT_ROOT'] . "/files/kcfinder/pages"
        );

        $this->set('mainPagesForDropdown', $this->Page->getMainPagesForDropdown($pageId));

        if ($pageId > 0) {
            $unsavedPage = $this->Page->find('all', array(
                'conditions' => array(
                    'Pages.id_page' => $pageId
                )
            ))->first();
        } else {
            // default values for new pages
            $unsavedPage = array(
                'Pages' => array(
                    'active' => APP_ON,
                    'position' => 10
                )
            );
        }
        $this->set('title_for_layout', 'Seite bearbeiten');

        if (empty($this->request->data)) {
            $this->request->data = $unsavedPage;
        } else {
            // validate data - do not use $this->Page->saveAll()
            $this->Page->id = $pageId;

            $this->request->data['Pages']['extern_url'] = StringComponent::addHttpToUrl($this->request->data['Pages']['extern_url']);

            $this->Page->set($this->request->data['Pages']);

            // quick and dirty solution for stripping html tags, use html purifier here
            foreach ($this->request->data['Pages'] as $key => &$data) {
                if ($key != 'content') {
                    $data = strip_tags(trim($data));
                }
            }

            $errors = array();
            if (! $this->Page->validates()) {
                $errors = array_merge($errors, $this->Page->validationErrors);
            }

            if (empty($errors)) {
                $this->request->data['Pages']['id_customer'] = $this->AppAuth->getUserId();

                $this->ActionLog = TableRegistry::get('ActionLogs');

                $this->Page->save($this->request->data['Pages'], array(
                    'validate' => false
                ));
                if (is_null($pageId)) {
                    $messageSuffix = 'erstellt.';
                    $actionLogType = 'page_added';
                } else {
                    $messageSuffix = 'geändert.';
                    $actionLogType = 'page_changed';
                }

                if (isset($this->request->data['Pages']['delete_page']) && $this->request->data['Pages']['delete_page']) {
                    $this->Page->saveField('active', APP_DEL, false);
                    $message = 'Die Seite "' . $this->request->data['Pages']['title'] . '" wurde erfolgreich gelöscht.';
                    $this->ActionLog->customSave('page_deleted', $this->AppAuth->getUserId(), $this->Page->id, 'pages', $message);
                    $this->Flash->success('Die Seite wurde erfolgreich gelöscht.');
                } else {
                    $message = 'Die Seite "' . $this->request->data['Pages']['title'] . '" wurde ' . $messageSuffix;
                    $this->ActionLog->customSave($actionLogType, $this->AppAuth->getUserId(), $this->Page->id, 'pages', $message);
                    $this->Flash->success('Die Seite wurde erfolgreich gespeichert.');
                }

                $this->redirect($this->data['referer']);
            } else {
                $this->Flash->error('Beim Speichern sind Fehler aufgetreten!');
            }
        }
    }

    public function index()
    {
        $conditions = array();

        $customerId = '';
        if (! empty($this->params['named']['customerId'])) {
            $customerId = $this->params['named']['customerId'];
            $conditions = array(
                'Pages.id_customer' => $customerId
            );
        }
        $this->set('customerId', $customerId);

        $conditions[] = 'Pages.active > ' . APP_DEL;

        $totalPagesCount = $this->Page->find('count', array(
            'conditions' => $conditions
        ));
        $this->set('totalPagesCount', $totalPagesCount);

        $pages = $this->Page->findAllGroupedByMenu($conditions);
        $this->set('pages', $pages);

        $this->set('title_for_layout', 'Seiten');

        $this->Customer = TableRegistry::get('Customers');
        $this->set('customersForDropdown', $this->Customer->getForDropdown());
    }
}
