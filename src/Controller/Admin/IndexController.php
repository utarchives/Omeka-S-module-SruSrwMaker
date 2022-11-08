<?php
namespace SruSrwMaker\Controller\Admin;

use SruSrwMaker\CsvFile;
use SruSrwMaker\Form\ImportForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    protected $config;
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    public function indexAction()
    {
        $view = new ViewModel();
        $form = $this->getForm(ImportForm::class);
        $view->form = $form;
        $response = $this->api()->search('srw_maps', ['sort_by' => 'id', 'sort_order' => 'asc']);
        $maps = $response->getContent();
        $view->setVariable('maps', $maps);
        return $view;
    }
    public function mapImportAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
                );
            $tmpFile = $post['csv']['tmp_name'];
            $csvFile = new CsvFile($this->config);
            $csvPath = $csvFile->getTempPath();
            $csvFile->moveToTemp($tmpFile);
            $csvFile->loadFromTempPath();
            $isUtf8 = $csvFile->isUtf8();
            if (! $csvFile->isUtf8()) {
                $this->messenger()->addError('File is not UTF-8 encoded.'); // @translate
                return $this->redirect()->toRoute('admin/sru-srw-maker/map-import');
            }
            $csv['csvpath'] = $csvPath;
            $dispatcher = $this->jobDispatcher();
            $job = $dispatcher->dispatch('SruSrwMaker\Job\ImportSrwMap', $csv);
            $this->messenger()->addSuccess('Importing in Job ID ' . $job->getId()); // @translate
        }
        $view = new ViewModel();
        $form = $this->getForm(ImportForm::class);
        $view->form = $form;
        return $view;
    }
}

