<?php
/**
 * The control file of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: control.php 5144 2013-07-15 06:37:03Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class contract extends control
{
    public $products = array();

    /**
     * Construct function.
     *
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);

        /* Load need modules. */
        $this->loadModel('product');
        $this->loadModel('story');
        $this->loadModel('release');
        $this->loadModel('tree');
        $this->loadModel('user');

        /* Get all products, if no, goto the create page. */
        $this->products = $this->product->getPairs('nocode');
        if(empty($this->products) and strpos(',create,index,showerrornone,', $this->methodName) === false and $this->app->getViewType() != 'mhtml') $this->locate($this->createLink('product', 'create'));
        $this->view->products = $this->products;
    }

    /**
     * Index page, to browse.
     *
     * @param  string $locate     locate to browse page or not. If not, display all products.
     * @param  int    $productID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function index($locate = 'auto', $productID = 0, $status = 'noclosed', $orderBy = 'order_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
        if($this->config->global->flow == 'onlyTest') $this->locate($this->createLink($this->moduleName, 'build'));
        if($locate == 'yes') $this->locate($this->createLink($this->moduleName, 'browse'));

        if($this->app->getViewType() != 'mhtml') unset($this->lang->product->menu->index);
        $productID = $this->product->saveState($productID, $this->products);
        $branch    = (int)$this->cookie->preBranch;
        $this->product->setMenu($this->products, $productID, $branch);

        if(common::hasPriv('product', 'create')) $this->lang->modulePageActions = html::a($this->createLink('product', 'create'), "<i class='icon icon-sm icon-plus'></i> " . $this->lang->product->create, '', "class='btn btn-primary'");

        $this->view->title         = $this->lang->product->index;
        $this->view->position[]    = $this->lang->product->index;
        $this->display();
    }

    /**
     * project
     *
     * @param  string $status
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function project($status = 'all', $productID = 0, $branch = 0)
    {
        $this->product->setMenu($this->products, $productID, $branch);

        $this->app->loadLang('my');
        $this->view->projectStats  = $this->loadModel('project')->getProjectStats($status, $productID, $branch);

        $this->view->title      = $this->products[$productID] . $this->lang->colon . $this->lang->product->project;
        $this->view->position[] = $this->products[$productID];
        $this->view->position[] = $this->lang->product->project;
        $this->view->productID  = $productID;
        $this->view->status     = $status;
        $this->display();
    }

    /**
     * Browse a product.
     *
     * @param  int    $productID
     * @param  string $browseType
     * @param  int    $param
     * @param  string $storyType requirement|story
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse($productID = 0, $branch = '', $browseType = '', $param = 0, $storyType = 'story', $orderBy = '', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Lower browse type. */
        $browseType = strtolower($browseType);

        /* Load datatable. */
        $this->loadModel('datatable');

        /* Save session. */
        $this->session->set('storyList',   $this->app->getURI(true));
        $this->session->set('productList', $this->app->getURI(true));

        /* Set product, module and query. */
        $productID = $this->product->saveState($productID, $this->products);
        $branch    = ($branch === '') ? (int)$this->cookie->preBranch : (int)$branch;
        setcookie('preProductID', $productID, $this->config->cookieLife, $this->config->webRoot, '', false, true);
        setcookie('preBranch', (int)$branch, $this->config->cookieLife, $this->config->webRoot, '', false, true);

        if($this->cookie->preProductID != $productID or $this->cookie->preBranch != $branch or $browseType == 'bybranch')
        {
            $_COOKIE['storyModule'] = 0;
            setcookie('storyModule', 0, 0, $this->config->webRoot, '', false, false);
        }

        if($browseType == 'bymodule' or $browseType == '')
        {
            setcookie('storyModule', (int)$param, 0, $this->config->webRoot, '', false, false);
            $_COOKIE['storyBranch'] = 0;
            setcookie('storyBranch', 0, 0, $this->config->webRoot, '', false, false);
            if($browseType == '') setcookie('treeBranch', (int)$branch, 0, $this->config->webRoot, '', false, false);
        }
        if($browseType == 'bybranch') setcookie('storyBranch', (int)$branch, 0, $this->config->webRoot, '', false, false);

        $moduleID = ($browseType == 'bymodule') ? (int)$param : (($browseType == 'bysearch' or $browseType == 'bybranch') ? 0 : ($this->cookie->storyModule ? $this->cookie->storyModule : 0));
        $queryID  = ($browseType == 'bysearch') ? (int)$param : 0;

        /* Set menu. */
        $this->product->setMenu($this->products, $productID, $branch);

        /* Set moduleTree. */
        $createModuleLink = $storyType == 'story' ? 'createStoryLink' : 'createRequirementLink';
        if($browseType == '')
        {
            setcookie('treeBranch', (int)$branch, 0, $this->config->webRoot, '', false, false);
            $browseType = 'unclosed';
            $moduleTree = $this->tree->getTreeMenu($productID, $viewType = 'story', $startModuleID = 0, array('treeModel', $createModuleLink), '', $branch);
        }
        else
        {
            $moduleTree = $this->tree->getTreeMenu($productID, $viewType = 'story', $startModuleID = 0, array('treeModel', $createModuleLink), '', (int)$this->cookie->treeBranch);
        }

        if($browseType != 'bymodule' and $browseType != 'bybranch') $this->session->set('storyBrowseType', $browseType);
        if(($browseType == 'bymodule' or $browseType == 'bybranch') and $this->session->storyBrowseType == 'bysearch') $this->session->set('storyBrowseType', 'unclosed');

        /* Process the order by field. */
        if(!$orderBy) $orderBy = $this->cookie->productStoryOrder ? $this->cookie->productStoryOrder : 'id_desc';
        setcookie('productStoryOrder', $orderBy, 0, $this->config->webRoot, '', false, true);

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Get stories. */
        $stories = $this->product->getStories($productID, $branch, $browseType, $queryID, $moduleID, $storyType, $sort, $pager);

        /* Process the sql, get the conditon partion, save it to session. */
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'story', $browseType != 'bysearch');

        /* Get related tasks, bugs, cases count of each story. */
        $storyIdList = array();
        foreach($stories as $story)
        {
            $storyIdList[$story->id] = $story->id;
            if(!empty($story->children))
            {
                foreach($story->children as $child) $storyIdList[$child->id] = $child->id;
            }
        }
        $storyTasks = $this->loadModel('task')->getStoryTaskCounts($storyIdList);
        $storyBugs  = $this->loadModel('bug')->getStoryBugCounts($storyIdList);
        $storyCases = $this->loadModel('testcase')->getStoryCaseCounts($storyIdList);

        /* Change for requirement story title. */
        if($storyType == 'requirement' and !empty($this->config->URAndSR))
        {
            $this->lang->story->title = str_replace($this->lang->SRCommon, $this->lang->URCommon, $this->lang->story->title);
            $this->config->product->search['fields']['title'] = $this->lang->story->title;
        }

        /* Build search form. */
        $actionURL = $this->createLink('product', 'browse', "productID=$productID&branch=$branch&browseType=bySearch&queryID=myQueryID&storyType=$storyType");
        $this->config->product->search['onMenuBar'] = 'yes';
        $this->product->buildSearchForm($productID, $this->products, $queryID, $actionURL);

        $showModule = !empty($this->config->datatable->productBrowse->showModule) ? $this->config->datatable->productBrowse->showModule : '';
        $this->view->modulePairs = $showModule ? $this->tree->getModulePairs($productID, 'story', $showModule) : array();

        /* Assign. */
        $this->view->title         = $this->products[$productID]. $this->lang->colon . $this->lang->product->browse;
        $this->view->position[]    = $this->products[$productID];
        $this->view->position[]    = $this->lang->product->browse;
        $this->view->productID     = $productID;
        $this->view->product       = $this->product->getById($productID);
        $this->view->productName   = $this->products[$productID];
        $this->view->moduleID      = $moduleID;
        $this->view->stories       = $stories;
        $this->view->plans         = $this->loadModel('productplan')->getPairs($productID, $branch);
        $this->view->summary       = $this->product->summary($stories, $storyType);
        $this->view->moduleTree    = $moduleTree;
        $this->view->parentModules = $this->tree->getParents($moduleID);
        $this->view->pager         = $pager;
        $this->view->users         = $this->user->getPairs('noletter|pofirst|nodeleted');
        $this->view->orderBy       = $orderBy;
        $this->view->browseType    = $browseType;
        $this->view->modules       = $this->tree->getOptionMenu($productID, $viewType = 'story', 0, $branch);
        $this->view->moduleID      = $moduleID;
        $this->view->moduleName    = $moduleID ? $this->tree->getById($moduleID)->name : $this->lang->tree->all;
        $this->view->branch        = $branch;
        $this->view->branches      = $this->loadModel('branch')->getPairs($productID);
        $this->view->storyStages   = $this->product->batchGetStoryStage($stories);
        $this->view->setModule     = true;
        $this->view->storyTasks    = $storyTasks;
        $this->view->storyBugs     = $storyBugs;
        $this->view->storyCases    = $storyCases;
        $this->view->param         = $param;
        $this->view->products      = $this->products;
        $this->view->storyType     = $storyType;
        $this->display();
    }

    /**
     * Create a contract.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        if(!empty($_POST))
        {
            $contract = $this->contract->create();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('contract', $contract, 'opened');
            $this->executeHooks($contract);
            $locate = $this->createLink($this->moduleName, 'view', "contractID=$contract");
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locate));
            
        }

        $rootID = key($this->products);
        if($this->session->product) $rootID = $this->session->product;
        $this->product->setMenu($this->products, $rootID);

        $this->loadModel('user');
        $poUsers = $this->user->getPairs('nodeleted|noclosed',  '', $this->config->maxCount);
        $this->view->title      = $this->lang->product->create;
        $this->view->position[] = $this->view->title;
        $this->view->groups     = $this->loadModel('group')->getPairs();
        $this->view->poUsers    = $poUsers;
        /* need to set which user should be select*/

        $this->view->rootID     = $rootID;
        $this->display();
    }
    public function createInvoice()
    {
        if(!empty($_POST))
        {
            foreach($_FILES['files']['type'] as $value){
                if(strpos($value, 'pdf')===false){
                    $this->send(array('status'=>"fail",'message'=>"only accept softcopy with pdf format"));
                }
            }
            if(empty($_FILES)){            
                $this->send(array('status'=>"fail",'message'=>"pleases attact the invoice softcopy!"));
            }
            $invoice = $this->contract->createInvoice();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('invoice', $invoice, 'opened');
            //$this->executeHooks($invoice);
            $locate = $this->createLink("contract", 'invoice', "invoice=$contract");
        $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess) /*, 'locate' => $locate)*/ );
            
        }

        $rootID = key($this->products);
        if($this->session->product) $rootID = $this->session->product;
        $this->product->setMenu($this->products, $rootID);
        
        $contractOption=$this->dao->select('id,contractName')->from('zt_contract')->where('appointedParty')->eq('admin')->andWhere('status')->eq('normal')->fetchPairs();
        if(!$contractOption){
            echo js::alert("no contract for you to create a invoice");
            die("<script>history.back()</script>");
        }
        $this->view->contractOption=$contractOption;
        $this->loadModel('user');
        $poUsers = $this->user->getPairs('nodeleted|noclosed',  '', $this->config->maxCount);
        /* need to set which user should be select*/
        $this->view->title      = $this->lang->product->create;
        $this->view->position[] = $this->view->title;
        $this->view->groups     = $this->loadModel('group')->getPairs();
        $this->view->rootID     = $rootID;
        $this->display();
    }
    /**
     * Edit a product.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function edit($productID, $action = 'edit', $extra = '')
    {
        if(!empty($_POST))
        {
            $changes = $this->product->update($productID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($action == 'undelete')
            {
                $this->loadModel('action');
                $this->dao->update(TABLE_PRODUCT)->set('deleted')->eq(0)->where('id')->eq($productID)->exec();
                $this->dao->update(TABLE_ACTION)->set('extra')->eq(ACTIONMODEL::BE_UNDELETED)->where('id')->eq($extra)->exec();
                $this->action->create('product', $productID, 'undeleted');
            }
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('product', $productID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($productID);
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('view', "product=$productID")));
        }

        $this->product->setMenu($this->products, $productID);

        $product = $this->product->getById($productID);

        $this->loadModel('user');
        $poUsers = $this->user->getPairs('nodeleted|pofirst',  $product->PO, $this->config->maxCount);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["PO"] = $this->config->user->moreLink;

        $qdUsers = $this->user->getPairs('nodeleted|qdfirst',  $product->QD, $this->config->maxCount);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["QD"] = $this->config->user->moreLink;

        $rdUsers = $this->user->getPairs('nodeleted|devfirst', $product->RD, $this->config->maxCount);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["RD"] = $this->config->user->moreLink;

        $this->view->title      = $this->lang->product->edit . $this->lang->colon . $product->name;
        $this->view->position[] = html::a($this->createLink($this->moduleName, 'browse'), $product->name);
        $this->view->position[] = $this->lang->product->edit;
        $this->view->product    = $product;
        $this->view->groups     = $this->loadModel('group')->getPairs();
        $this->view->poUsers    = $poUsers;
        $this->view->qdUsers    = $qdUsers;
        $this->view->rdUsers    = $rdUsers;
        $this->view->lines      = array('') + $this->loadModel('tree')->getLinePairs();

        unset($this->lang->product->typeList['']);
        $this->display();
    }

    /**
     * Batch edit products.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function batchEdit($productID = 0)
    {
        if($this->post->names)
        {
            $allChanges = $this->product->batchUpdate();
            if(!empty($allChanges))
            {
                foreach($allChanges as $productID => $changes)
                {
                    if(empty($changes)) continue;

                    $actionID = $this->loadModel('action')->create('product', $productID, 'Edited');
                    $this->action->logHistory($actionID, $changes);
                }
            }
            die(js::locate($this->session->productList, 'parent'));
        }

        $this->product->setMenu($this->products, $productID);

        $productIDList = $this->post->productIDList ? $this->post->productIDList : die(js::locate($this->session->productList, 'parent'));

        /* Set custom. */
        foreach(explode(',', $this->config->product->customBatchEditFields) as $field) $customFields[$field] = $this->lang->product->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->product->custom->batchEditFields;

        $products      = $this->dao->select('*')->from(TABLE_PRODUCT)->where('id')->in($productIDList)->fetchAll('id');
        $appendPoUsers = $appendQdUsers = $appendRdUsers = array();
        foreach($products as $product)
        {
            $appendPoUsers[$product->PO] = $product->PO;
            $appendQdUsers[$product->QD] = $product->QD;
            $appendRdUsers[$product->RD] = $product->RD;
        }

        $this->loadModel('user');
        $poUsers = $this->user->getPairs('nodeleted|pofirst', $appendPoUsers);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["PO"] = $this->config->user->moreLink;

        $qdUsers = $this->user->getPairs('nodeleted|qdfirst', $appendQdUsers);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["QD"] = $this->config->user->moreLink;

        $rdUsers = $this->user->getPairs('nodeleted|devfirst', $appendRdUsers);
        if(!empty($this->config->user->moreLink)) $this->config->moreLinks["RD"] = $this->config->user->moreLink;

        $this->view->title         = $this->lang->product->batchEdit;
        $this->view->position[]    = $this->lang->product->batchEdit;
        $this->view->lines         = array('') + $this->tree->getLinePairs();
        $this->view->productIDList = $productIDList;
        $this->view->products      = $products;
        $this->view->poUsers       = $poUsers;
        $this->view->qdUsers       = $qdUsers;
        $this->view->rdUsers       = $rdUsers;

        unset($this->lang->product->typeList['']);
        $this->display();
    }

    /**
     * Close product.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function close($productID)
    {
        $product = $this->product->getById($productID);
        $actions = $this->loadModel('action')->getList('product', $productID);

        if(!empty($_POST))
        {
            $changes = $this->product->close($productID);
            if(dao::isError()) die(js::error(dao::getError()));

            if($this->post->comment != '' or !empty($changes))
            {
                $actionID = $this->action->create('product', $productID, 'Closed', $this->post->comment);
                $this->action->logHistory($actionID, $changes);
            }

            $this->executeHooks($productID);

            die(js::reload('parent.parent'));
        }

        $this->product->setMenu($this->products, $productID);

        $this->view->product    = $product;
        $this->view->title      = $this->view->product->name . $this->lang->colon .$this->lang->close;
        $this->view->position[] = $this->lang->close;
        $this->view->actions    = $actions;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    /**
     * View a product.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function view($productID)
    {
        $product = $this->product->getStatByID($productID);
        $contractID=$productID;
        $contract = $this->dao->select('*')->from('zt_contract')->where('id')->eq($contractID)->fetch();
        $contractAP=$this->dao->select('*')->from('zt_approval')->where('objectType')->eq('contract')->andWhere("objectID")->eq($contractID)->fetchALL();
        var_dump($contract);
        var_dump($contractAP);

        
        if(!$contract) die(js::error($this->lang->notFound) . js::locate('back'));

        $product->desc = $this->loadModel('file')->setImgSize($product->desc);
        $this->product->setMenu($this->products, $productID);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager(0, 30, 1);

        $this->executeHooks($productID);

        $this->view->title      = $contract->contractName . $this->lang->colon . $this->lang->product->view;
        $this->view->position[] = html::a($this->createLink($this->moduleName, 'browse'), $product->name);
        $this->view->position[] = $this->lang->product->view;
        $this->view->product    = $product;
        $this->view->actions    = $this->loadModel('action')->getList('contract', $contractID);
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->groups     = $this->loadModel('group')->getPairs();
        $this->view->lines      = array('') + $this->loadModel('tree')->getLinePairs();
        $this->view->branches   = $this->loadModel('branch')->getPairs($productID);
        $this->view->dynamics   = $this->loadModel('action')->getDynamic('all', 'all', 'date_desc', $pager, $productID);
        $this->view->roadmaps   = $this->product->getRoadmap($productID, 0, 6);

        $this->display();
    }



    /**
     * Road map of a product.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function roadmap($productID, $branch = 0)
    {
        $this->product->setMenu($this->products, $productID, $branch);

        $this->session->set('releaseList',     $this->app->getURI(true));
        $this->session->set('productPlanList', $this->app->getURI(true));

        $product = $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch();
        if(empty($product)) $this->locate($this->createLink('product', 'showErrorNone', 'fromModule=product'));

        $this->view->title      = $product->name . $this->lang->colon . $this->lang->product->roadmap;
        $this->view->position[] = html::a($this->createLink($this->moduleName, 'browse'), $product->name);
        $this->view->position[] = $this->lang->product->roadmap;
        $this->view->product    = $product;
        $this->view->roadmaps   = $this->product->getRoadmap($productID, $branch);
        $this->view->branches   = $product->type == 'normal' ? array(0 => '') : $this->loadModel('branch')->getPairs($productID);

        $this->display();
    }

    /**
     * Product dynamic.
     *
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function dynamic($productID = 0, $type = 'today', $param = '', $recTotal = 0, $date = '', $direction = 'next')
    {
        /* Save session. */
        $uri = $this->app->getURI(true);
        $this->session->set('productList',     $uri);
        $this->session->set('productPlanList', $uri);
        $this->session->set('releaseList',     $uri);
        $this->session->set('storyList',       $uri);
        $this->session->set('projectList',     $uri);
        $this->session->set('taskList',        $uri);
        $this->session->set('buildList',       $uri);
        $this->session->set('bugList',         $uri);
        $this->session->set('caseList',        $uri);
        $this->session->set('testtaskList',    $uri);

        $this->product->setMenu($this->products, $productID);

        /* Append id for secend sort. */
        $orderBy = $direction == 'next' ? 'date_desc' : 'date_asc';
        $sort    = $this->loadModel('common')->appendOrder($orderBy);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage = 50, $pageID = 1);

        /* Set the user and type. */
        $account = $type == 'account' ? $param : 'all';
        $period  = $type == 'account' ? 'all'  : $type;
        $date    = empty($date) ? '' : date('Y-m-d', $date);
        $actions = $this->loadModel('action')->getDynamic($account, $period, $sort, $pager, $productID, 'all', $date, $direction);

        /* The header and position. */
        $this->view->title      = $this->products[$productID] . $this->lang->colon . $this->lang->product->dynamic;
        $this->view->position[] = html::a($this->createLink($this->moduleName, 'browse'), $this->products[$productID]);
        $this->view->position[] = $this->lang->product->dynamic;

        /* Assign. */
        $this->view->productID  = $productID;
        $this->view->type       = $type;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter|nodeleted|noclosed');
        $this->view->account    = $account;
        $this->view->orderBy    = $orderBy;
        $this->view->param      = $param;
        $this->view->pager      = $pager;
        $this->view->dateGroups = $this->action->buildDateGroup($actions, $direction, $type);
        $this->view->direction  = $direction;
        $this->display();
    }

    /**
     * AJAX: get projects of a product in html select.
     *
     * @param  int    $productID
     * @param  int    $projectID
     * @param  string $number
     * @access public
     * @return void
     */
    public function ajaxGetProjects($productID, $projectID = 0, $branch = 0, $number = '')
    {
        if($productID == 0)
        {
            $projects = array(0 => '') + $this->loadModel('project')->getPairs();
        }
        else
        {
            $projects = $this->product->getProjectPairs($productID, $branch ? "0,$branch" : $branch, $params = 'nodeleted');
        }
        if($this->app->getViewType() == 'json') die(json_encode($projects));

        if($number === '')
        {
            die(html::select('project', $projects, $projectID, "class='form-control' onchange='loadProjectRelated(this.value)'"));
        }
        else
        {
            $projectName = "projects[$number]";
            $projects    = empty($projects) ? array('' => '') : $projects;
            die(html::select($projectName, $projects, '', "class='form-control' onchange='loadProjectBuilds($productID, this.value, $number)'"));
        }
    }

    /**
     * AJAX: get plans of a product in html select.
     *
     * @param  int    $productID
     * @param  int    $planID
     * @param  bool   $needCreate
     * @param  string $expired
     * @access public
     * @return void
     */
    public function ajaxGetPlans($productID, $branch = 0, $planID = 0, $fieldID = '', $needCreate = false, $expired = '')
    {
        $plans = $this->loadModel('productplan')->getPairs($productID, $branch, $expired);
        $field = $fieldID ? "plans[$fieldID]" : 'plan';
        $output = html::select($field, $plans, $planID, "class='form-control chosen'");
        if(count($plans) == 1 and $needCreate)
        {
            $output .= "<div class='input-group-btn'>";
            $output .= html::a($this->createLink('productplan', 'create', "productID=$productID&branch=$branch", '', true), "<i class='icon icon-plus'></i>", '', "class='btn btn-icon' data-toggle='modal' data-type='iframe' data-width='95%' title='{$this->lang->productplan->create}'");
            $output .= '</div>';
            $output .= "<div class='input-group-btn'>";
            $output .= html::a("javascript:void(0)", "<i class='icon icon-refresh'></i>", '', "class='btn btn-icon refresh' data-toggle='tooltip' title='{$this->lang->refresh}' onclick='loadProductPlans($productID)'");
            $output .= '</div>';
        }
        die($output);
    }

    /**
     * Drop menu page.
     *
     * @param  int    $productID
     * @param  string $module
     * @param  string $method
     * @param  string $extra
     * @access public
     * @return void
     */
    public function ajaxGetDropMenu($productID, $module, $method, $extra)
    {
        $this->view->link      = $this->product->getProductLink($module, $method, $extra);
        $this->view->productID = $productID;
        $this->view->module    = $module;
        $this->view->method    = $method;
        $this->view->extra     = $extra;

        $products = $this->dao->select('*')->from(TABLE_PRODUCT)->where('id')->in(array_keys($this->products))->orderBy('`order` desc')->fetchAll('id');

        /* Sort products as lines' order first. */
        $lines = $this->loadModel('tree')->getLinePairs($useShort = true);
        $productList = array();
        foreach($lines as $id => $name)
        {
            foreach($products as $key => $product)
            {
                if($product->line == $id)
                {
                    $product->name = $name . '/' . $product->name;
                    $productList[] = $product;
                    unset($products[$key]);
                }
            }
        }
        $productList = array_merge($productList, $products);

        $this->view->products  = $productList;
        $this->display();
    }

    /**
     * Update order.
     *
     * @access public
     * @return void
     */
    public function updateOrder()
    {
        $idList   = explode(',', trim($this->post->products, ','));
        $orderBy  = $this->post->orderBy;
        if(strpos($orderBy, 'order') === false) return false;

        $products = $this->dao->select('id,`order`')->from(TABLE_PRODUCT)->where('id')->in($idList)->orderBy($orderBy)->fetchPairs('order', 'id');
        foreach($products as $order => $id)
        {
            $newID = array_shift($idList);
            if($id == $newID) continue;
            $this->dao->update(TABLE_PRODUCT)->set('`order`')->eq($order)->where('id')->eq($newID)->exec();
        }
    }

    /**
     * Show error no product when visit qa.
     *
     * @param  string $fromModule
     * @access public
     * @return void
     */
    public function showErrorNone($fromModule = 'bug')
    {
        $this->loadModel($fromModule)->setMenu($this->products, key($this->products));
        $this->lang->set('menugroup.product', 'qa');
        $this->lang->product->menu      = $this->lang->$fromModule->menu;
        $this->lang->product->menuOrder = $this->lang->$fromModule->menuOrder;

        $this->view->title = $this->lang->$fromModule->common;
        $this->display();
    }

    /**
     * All product.
     *
     * @param  int    $productID
     * @param  int    $line
     * @param  string $status
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function all($productID = 0, $line = 0, $status = 'noclosed', $orderBy = 'order_desc', $recTotal = 0, $recPerPage = 10, $pageID = 1)
    {
        $this->session->set('productList', $this->app->getURI(true));
        $productID = $this->product->saveState($productID, $this->products);
        $this->product->setMenu($this->products, $productID);

        /* Load pager and get tasks. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Save this url to session. */
        $uri = $this->app->getURI(true);
        $this->app->session->set('lineList', $uri);

        $this->app->loadLang('my');
        $this->view->title        = $this->lang->product->allProduct;
        $this->view->position[]   = $this->lang->product->allProduct;
        $this->view->productStats = $this->product->getStats($orderBy, $pager, $status, $line);
        $this->view->lineTree     = $this->loadModel('tree')->getTreeMenu(0, $viewType = 'line', $startModuleID = 0, array('treeModel', 'createLineLink'), array('productID' => $productID, 'status' => $status));
        $this->view->lines        = array('') + $this->tree->getLinePairs();
        $this->view->productID    = $productID;
        $this->view->line         = $line;
        $this->view->status       = $status;
        $this->view->orderBy      = $orderBy;
        $this->view->pager        = $pager;
        $this->display();
    }

    /**
     * Export product.
     *
     * @param  string    $status
     * @param  string    $orderBy
     * @access public
     * @return void
     */
    public function export($status, $orderBy)
    {
        if($_POST)
        {
            $productLang   = $this->lang->product;
            $productConfig = $this->config->product;

            /* Create field lists. */
            $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $productConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = zget($productLang, $fieldName);
                unset($fields[$key]);
            }

            $lines = $this->loadModel('tree')->getLinePairs();
            $productStats = $this->product->getStats($orderBy, null, $status);
            foreach($productStats as $i => $product)
            {
                $product->line             = zget($lines, $product->line, '');
                $product->activeStories    = (int)$product->stories['active'];
                $product->changedStories   = (int)$product->stories['changed'];
                $product->draftStories     = (int)$product->stories['draft'];
                $product->closedStories    = (int)$product->stories['closed'];
                $product->unResolvedBugs   = (int)$product->unResolved;
                $product->assignToNullBugs = (int)$product->assignToNull;

                if($this->post->exportType == 'selected')
                {
                    $checkedItem = $this->cookie->checkedItem;
                    if(strpos(",$checkedItem,", ",{$product->id},") === false) unset($productStats[$i]);
                }
            }
            if(isset($this->config->bizVersion)) list($fields, $productStats) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $productStats);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $productStats);
            $this->post->set('kind', 'product');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->display();
    }

    // start the workflow of appove invoce
    public function submit($invoiceID ='0')
    {
        $invoice=$this->contract->getByID($invoiceID);
        $contract=$this->dao->select('*')->from('zt_contract')->where('id')->eq($invoice->contractID)->fetch();
        if($this->app->user->account != $contract->appointedParty){
            echo js::alert("you can not submit this invoice");
            echo "<script>history.back()</script>";
            die();
        }
        if($invoice->status!='panding'){
            echo js::alert("The invoice is not ready for submit, Pleases check the status");
            echo "<script>history.back()</script>";
            die();
        }
        $invoice->status="submitted";
        $invoice->submitDate=helper::now();
        $this->dao->update('zt_invoice')->data($invoice)->where('id')->eq($invoiceID)->exec();
        $this->contract->submit($invoiceID,$contract->id);
        $this->loadModel('action')->create('invoice', $invoiceID, 'submitted');
        echo "success";
    }
        
        /**
     * Delete a contract.
     *
     * @param  int   
     * @access public
     * @return void
     */
    public function delete($contractID)//delete contract
    {
        $contract=$this->dao->select('*')->from('zt_contract')->where('id')->eq('contractID')->fetch();
        if($contract->contractManager!=$this->app->user->account || $contract->createdBy!=$this->app->user->account){
            echo js::alert("you do not have the right to delete this contact");
            die("</br>fail");
        }
        if($contract->status!='normal'){
           //echo js::alert("This invoice can not be delete");
           // echo "<script>history.back()</script>";
           //die();
        }
        $contract->deleted="1";
        $contract->lastEdit=helper::now();
        $this->dao->update('zt_contract')->data($contract)->where('id')->eq($contractID)->exec();
        echo "success";
    }
    /**
     * delete the Invoice
     * @param  int   
     * @access public
     * @return void
     */
    public function deleteInvoice($invoiceID ='0')//soft delete invoice(keep files)
    {   

        $invoice=$this->contract->getByID($invoiceID);
        if($invoice->status!='panding'){
            echo js::alert("This invoice can not be delete");
           // echo "<script>history.back()</script>";
            die();
        }
        $contract=$this->dao->select('*')->from('zt_contract')->where('id')->eq($invoice->contractID)->fetch();
        if($this->app->user->account != $contract->appointedParty){//check owner
            echo js::alert("you can not delete this invoice");
           // echo "<script>history.back()</script>";
            die();
        }

        $invoice->deleted="1";
        $invoice->lastEdit=helper::now();
        $this->dao->update('zt_invoice')->data($invoice)->where('id')->eq($invoiceID)->exec();
        echo "success";
    }

    public function approve($invoiceID ='0',$act="")
    {
        $ap=$this->dao->select("*")->from(" zt_invoice, zt_approval")
                ->where('zt_invoice.id')->eq("$invoiceID")
                ->andWhere('zt_approval.objectID')->eq($invoiceID)
                ->andWhere('zt_approval.objectType')->eq('invoice')
                ->andWhere('zt_approval.user')->eq($this->app->user->account)
                ->andWhere("zt_approval.status = 'waiting' and zt_invoice.step=zt_approval.order")
                ->fetch();
        if(!$ap){
            echo js::alert('You can not approve this invoice now');
            echo "<script>history.back()</script>";
            die();
        }
        $approval=$this->dao->select('*')->from('zt_approval')->where('id')->eq($ap->id)->fetch();
        if(!$approval || $approval->user!=$this->app->user->account){
            var_dump($approval);
            die("system error");
        }
        $approval->approveDate=helper::now();
        //$approval->signature=user::getSign();

        if($act!=""||!empty($act)||isset($act)){
            $approval->status="approved";
	    echo "approved";
        }else{
            $approval->status="rejected";
	    die('rejected');
        }
        //add desc
        $this->dao->update("zt_approval")->data($approval)->where('id')->eq($approval->id)->exec();
        echo("success");
        $sameStep = $this->dao->select('*')->from('zt_approval')//check approve stage
        ->where('`order`')->eq($approval->order)
        ->andWhere('status')->eq('waiting')
        ->andWhere('objectType')->eq('invoice')
        ->andWhere('objectID')->eq($approval->objectID)
        ->fetch();
	var_dump($sameStep);
        if($sameStep){
            echo js::alert('waiting for same step approver');
           // die("<script>history.back()</script>");
        }

        $nextStep = $this->dao->select('*')->from('zt_approval')//check approve stage
        ->where('status')->eq('waiting')
        ->andWhere('objectType')->eq('invoice')
        ->andWhere('objectID')->eq($approval->objectID)
        ->andwhere("`order` > $approval->order order by `order`")
        ->fetchALL();
        //if(!$nextStep){//invoice approval finish, notify contract man
        
            //checking
        /**
      
         * if all people finished
         * close the invoice
         */

        //}
        $invoice =$this->contract->getByID($invoiceID);
        $users=array();
        foreach($nextStep as $value){//get next secquence approver
            if(empty($users)){
                array_push($users,$value->user);
                $invoice->step=$value->order;
                $this->dao->update('zt_invoice')->data($invoice)->where('id')->eq($invoiceID)->exec();
            }else{
                if($value->order==$users['0']->order){
                    array_pust($users,$value->user);
                }else{
                    break;
                }
            }
        }
        $this->contract->sendApproveNote($approval->objectID,$users);// send mail to next step
        echo 'success';


    }
}
