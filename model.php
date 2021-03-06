<?php
/**
 * The model file of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: model.php 5118 2013-07-12 07:41:41Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class contractModel extends model
{
    /**
     *
     * Set menu.
     *
     * @param array  $products
     * @param int    $productID
     * @param int    $branch
     * @param int    $module
     * @param string $moduleType
     * @param string $extra
     *
     * @access public
     * @return void
     */
    public function setMenu($products, $productID, $branch = 0, $module = 0, $moduleType = '', $extra = '')
    {
        
        if(common::hasPriv('contract', 'all')){

        }
        /* Has access privilege?. */
        //if($products and !isset($products[$productID]) and !$this->checkPriv($productID)) $this->accessDenied();

        $currentModule = $this->app->getModuleName();
        $currentMethod = $this->app->getMethodName();
        $selectHtml = $this->select($products, $productID, $currentModule, $currentMethod, $extra, $branch, $module, $moduleType);
        $label = $this->lang->product->index;
        if($this->config->global->flow != 'full') $label = $this->lang->product->all;
        if($currentModule == 'product' && $currentMethod == 'all')    $label = $this->lang->product->all;
        if($currentModule == 'product' && $currentMethod == 'create') $label = $this->lang->product->create;
        $pageNav  = '';
            $pageNav  = '<div class="btn-group angle-btn' . ($currentMethod == 'index' ? ' active' : '') . '"><div class="btn-group"><button data-toggle="dropdown" type="button" class="btn">' . $label . ' <span class="caret"></span></button>';
            $pageNav .= '<ul class="dropdown-menu">';
            if($this->config->global->flow == 'full' && common::hasPriv('product', 'index')) $pageNav .= '<li>' . html::a(helper::createLink('product', 'index', 'locate=no'), '<i class="icon icon-home"></i> ' . $this->lang->product->index) . '</li>';
            if(common::hasPriv('product', 'all')) $pageNav .= '<li>' . html::a(helper::createLink('product', 'all'), '<i class="icon icon-cards-view"></i> ' . $this->lang->product->all) . '</li>';
            if(common::hasPriv('product', 'create')) $pageNav .= '<li>' . html::a(helper::createLink('product', 'create'), '<i class="icon icon-plus"></i> ' . $this->lang->product->create) . '</li>';
            
            $pageNav .= '</ul></div></div>';
            $pageNav .= $selectHtml;
        
        $pageActions = '';
        $this->lang->modulePageNav     = $pageNav;
        $this->lang->modulePageActions = $pageActions;
        foreach($this->lang->product->menu as $key => $menu)
        {
            $replace = $productID;
            common::setMenuVars($this->lang->product->menu, $key, $replace);
        }
    }

    /**
     * Create the select code of products.
     *
     * @param  array  $products
     * @param  int    $productID
     * @param  string $currentModule
     * @param  string $currentMethod
     * @param  string $extra
     *
     * @access public
     * @return string
     */
    public function select($products, $productID, $currentModule, $currentMethod, $extra = '', $branch = 0, $module = 0, $moduleType = '')
    {
        if(!$productID)
        {
            unset($this->lang->product->menu->branch);
            return;
        }
        setCookie("lastProduct", $productID, $this->config->cookieLife, $this->config->webRoot, '', false, true);
        $currentProduct = $this->loadModel('product')->getById($productID);
        $this->session->set('currentProductType', $currentProduct->type);

        $dropMenuLink = helper::createLink('contract', 'ajaxGetDropMenu', "objectID=$productID&module=$currentModule&method=$currentMethod&extra=$extra");
        $output  = "<div class='btn-group angle-btn'><div class='btn-group'><button data-toggle='dropdown' type='button' class='btn btn-limit' id='currentItem' title='{$currentProduct->name}'>{$currentProduct->name} <span class='caret'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
        $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
        $output .= "</div></div>";
/*
        if($currentProduct->type == 'normal') unset($this->lang->product->menu->branch);
        if($currentProduct->type != 'normal')
        {
            $this->lang->product->branch = sprintf($this->lang->product->branch, $this->lang->product->branchName[$currentProduct->type]);
            $this->lang->product->menu->branch = str_replace('@branch@', $this->lang->product->branchName[$currentProduct->type], $this->lang->product->menu->branch);
            $branches   = $this->loadModel('branch')->getPairs($productID);
            $branchName = isset($branches[$branch]) ? $branches[$branch] : $branches[0];
     
                $dropMenuLink = helper::createLink('branch', 'ajaxGetDropMenu', "objectID=$productID&module=$currentModule&method=$currentMethod&extra=$extra");
                $output .= "<div class='btn-group'><button id='currentBranch' data-toggle='dropdown' type='button' class='btn btn-limit'>{$branchName} <span class='caret'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
                $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
                $output .= "</div></div>";
       

        if($this->config->global->flow == 'onlyTest' and $moduleType)
        {
            if($module) $module = $this->loadModel('tree')->getById($module);
            $moduleName = $module ? $module->name : $this->lang->tree->all;
            if(!$isMobile)
            {
                $dropMenuLink = helper::createLink('tree', 'ajaxGetDropMenu', "objectID=$productID&module=$currentModule&method=$currentMethod&extra=$extra");
                $output .= "<div class='btn-group'><button id='currentModule' data-toggle='dropdown' type='button' class='btn btn-limit'>{$moduleName} <span class='caret'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
                $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>';
                $output .= "</div></div>";
            }
            else
            {
                $output .= "<a id='currentModule' href=\"javascript:showSearchMenu('tree', '$productID', '$currentModule', '$currentMethod', '$extra')\">{$moduleName} <span class='icon-caret-down'></span></a><div id='currentBranchDropMenu' class='hidden affix enter-from-bottom layer'></div>";
            }
        }
                }
*/
        if(!$isMobile) $output .= '</div>';
  
        return $output;
    }

    /**
     * Save the product id user last visited to session.
     *
     * @param  int   $productID
     * @param  array $products
     * @access public
     * @return int
     */
    public function saveState($productID, $products)
    {
        if($productID > 0) $this->session->set('product', (int)$productID);
        if($productID == 0 and $this->cookie->lastProduct)    $this->session->set('product', (int)$this->cookie->lastProduct);
        if($productID == 0 and $this->session->product == '') $this->session->set('product', key($products));
        if(!isset($products[$this->session->product]))
        {
            $this->session->set('product', key($products));
            if($productID) $this->accessDenied();
        }
        if($this->cookie->preProductID != $productID)
        {
            $this->cookie->set('preBranch', 0);
            setcookie('preBranch', 0, $this->config->cookieLife, $this->config->webRoot, '', false, true);
        }

        return $this->session->product;
    }

    /**
     * Check privilege.
     *
     * @param  int    $product
     * @access public
     * @return bool
     */
    public function checkPriv($invoiceID)
    {

        if(empty($invoiceID)) return false;

        /* Is admin? */
        if($this->app->user->admin) return true;
	$invoice=$this->getByID($invoiceID);
	$contract=$this->getContractByID($invoice->contractID);
	$user=$this->app->user->account;
	$cm= json_decode($contract->contractManager,true);
	$apseq=$this->dao->select('user')->from('zt_approval')->where('objectType')->eq('contract')->andWhere('objectID')->eq($invoice->contractID)->fetchAll();
	if($user==$contract->appointedParty||in_array($user,$cm)||in_array($apseq)){
		return true;
	}else{
		return false;
	}
    }

    /**
     * Show accessDenied response.
     *
     * @access private
     * @return void
     */
    public function accessDenied()
    {
        echo(js::alert($this->lang->product->accessDenied));

        if(!$this->server->http_referer) die(js::locate(helper::createLink('product', 'index')));

        $loginLink = $this->config->requestType == 'GET' ? "?{$this->config->moduleVar}=user&{$this->config->methodVar}=login" : "user{$this->config->requestFix}login";
        if(strpos($this->server->http_referer, $loginLink) !== false) die(js::locate(helper::createLink('product', 'index')));

        die(js::locate('back'));
    }

    /**
     * Get invoice by id.
     *
     * @param  int    $productID
     * @access public
     * @return object
     */
    public function getById($invoiceID)
    {
        $invoice = $this->dao->findById($invoiceID)->from("zt_invoice")->fetch();
        if(!$invoice) return false;

        return $invoice;
        //return $this->loadModel('file')->replaceImgURL($invoice, 'description');
    }
    /**
     * Get contract by id.
     *
     * @param  int    $productID
     * @access public
     * @return object
     */
    public function getContractByID($contractID)
    {
        $contract = $this->dao->findById($contractID)->from("zt_contract")->fetch();
        if(!$contract) return false;

        return $contract;
    }


    /**
     * Get by idList.
     *
     * @param  array    $productIDList
     * @access public
     * @return array
     */
    public function getByIdList($productIDList)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCT)->where('id')->in($productIDList)->fetchAll('id');
    }

    /**
     * Get products.
     *
     * @param  string $status
     * @param  int    $limit
     * @param  int    $line
     * @access public
     * @return array
     */
    public function getList($status = 'all', $limit = 0, $line = 0)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->beginIF($line > 0)->andWhere('line')->eq($line)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->products)->fi()
            ->beginIF($status == 'noclosed')->andWhere('status')->ne('closed')->fi()
            ->beginIF($status != 'all' and $status != 'noclosed' and $status != 'involved')->andWhere('status')->in($status)->fi()
            ->beginIF($status == 'involved')
            ->andWhere('PO', true)->eq($this->app->user->account)
            ->orWhere('QD')->eq($this->app->user->account)
            ->orWhere('RD')->eq($this->app->user->account)
            ->orWhere('createdBy')->eq($this->app->user->account)
            ->markRight(1)
            ->fi()
            ->orderBy('`order` desc')
            ->beginIF($limit > 0)->limit($limit)->fi()
            ->fetchAll('id');
    }

    /**
     * Get product pairs.
     *
     * @param  string $mode
     * @return array
     */
    public function getPairs($mode = '')
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getProductPairs();

        $orderBy  = !empty($this->config->product->orderBy) ? $this->config->product->orderBy : 'isClosed';
        $products = $this->dao->select('*,  IF(INSTR(" closed", status) < 2, 0, 1) AS isClosed')
            ->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->beginIF(strpos($mode, 'noclosed') !== false)->andWhere('status')->ne('closed')->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->products)->fi()
            ->orderBy($orderBy)
            ->fetchPairs('id', 'name');
        return $products;
    }

    /**
     * Get products by project.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getProductsByProject($projectID)
    {
        if($this->config->global->flow == 'onlyTask') return array();

        return $this->dao->select('t1.product, t2.name')
            ->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')
            ->on('t1.product = t2.id')
            ->where('t1.project')->eq($projectID)
            ->andWhere('t2.deleted')->eq(0)
            ->orderBy('t2.order desc')
            ->fetchPairs();
    }

    /**
     * Get grouped products.
     *
     * @access public
     * @return void
     */
    public function getStatusGroups()
    {
        $products = $this->dao->select('id, name, status')->from(TABLE_PRODUCT)->where('deleted')->eq(0)->fetchGroup('status');
    }

    /**
     * Get ordered products 
     * 
     * @param  string $status 
     * @param  int    $num 
     * @access public
     * @return array
     */
    public function getOrderedProducts($status, $num = 0)
    {
        $products = $this->getList($status);
        if(empty($products)) return $products;

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

        $products = $mineProducts = $otherProducts = $closedProducts = array();
        foreach($productList as $product)
        {
            if(!$this->app->user->admin and !$this->checkPriv($product->id)) continue;
            if($product->status == 'normal' and $product->PO == $this->app->user->account) 
            {
                $mineProducts[$product->id] = $product;
            }
            elseif($product->status == 'normal' and $product->PO != $this->app->user->account) 
            {
                $otherProducts[$product->id] = $product;
            }
            elseif($product->status == 'closed')
            {
                $closedProducts[$product->id] = $product;
            }
        }
        $products = $mineProducts + $otherProducts + $closedProducts;

        if(empty($num)) return $products;
        return array_slice($products, 0, $num, true);
    }

    /**
     * Create a contract.
     *
     * @access public
     * @return int
     */
    public function create()
    {
        $contract = fixer::input('post')
            ->setIF($this->post->acl != 'custom', 'whitelist', '')
            ->setDefault('status', 'normal')
            ->setDefault('createdBy', $this->app->user->account)
            ->setDefault('createdDate', helper::now())
            ->join('whitelist', ',')
            ->remove('uid')
            ->remove('order')
            ->remove('ap')
            ->remove('sign')
            ->get();
	$contract->contractManager=json_encode($contract->contractManager);
        $this->dao->insert("zt_contract")->data($contract)->autoCheck()
            ->check('name', 'unique', "deleted = '0'")
            ->check('code', 'unique', "deleted = '0'")
            ->exec();
        $contractID = $this->dao->lastInsertID();

        if(dao::isError()) die(js::error('contractID#' . $contractID . dao::getError(true)));
        $i=0;
        for($i;$i<count($_POST['ap']);$i++){// create approve schema
            if($_POST['ap'][$i]=="" || empty($_POST['ap'][$i]) || !isset($_POST['ap'][$i])){
                continue;
            }else{
                $approval['objectType']='contract';
                $approval['objectID']=$contractID;
                $approval['user']=$_POST['ap'][$i];
                $approval['order']=isset($_POST['order'][$i])?$_POST['order'][$i]:'1';
                $approval['sign']=$_POST["sign"][$i];
                $approval['status']="waiting";
                $this->dao->insert("zt_approval")->data($approval)->exec();
            }
        }

        return $contractID;
        
    }
    /**
     * Create a invoice.
     *
     * @access public
     * @return int
     */
    public function createInvoice()
    {
        $invoice = fixer::input('post')
            ->setDefault('lastEdit', helper::now())
            ->setDefault('status', 'pending')
            ->remove('uid')
            ->remove('item')
            ->remove('labels')
            ->remove('files')
            ->remove('price')
            ->get();
        $this->dao->insert("zt_invoice")->data($invoice)->exec();
        $invoiceID = $this->dao->lastInsertID();
        if(dao::isError()) die(js::error('invoice#' . $invoiceID . dao::getError(true)));
        $i=0;
        for($i;$i<count($_POST['item']);$i++){//create invoice details
            if($_POST['item'][$i]=="" || empty($_POST['item'][$i]) || !isset($_POST['item'][$i])){
                continue;
            }else{
                $details['invoiceID']=$invoiceID;
                $details['item']=$_POST['item'][$i];
                $details['price']=isset($_POST['price'][$i])?$_POST['price'][$i]:'0';
                $this->dao->insert("zt_invoicedetails")->data($details)->exec();
            }
        }
        $this->loadModel('file')->updateObjectID($this->post->uid, $invoiceID, 'invoice');
        $files = $this->file->saveUpload('invoice', $invoiceID);
        return $invoiceID;
        
    }

    /**
     * Update a product.
     *
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function update($productID)
    {
        $productID  = (int)$productID;
        $oldProduct = $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch();
        $product = fixer::input('post')
            ->setIF($this->post->acl != 'custom', 'whitelist', '')
            ->join('whitelist', ',')
            ->stripTags($this->config->product->editor->edit['id'], $this->config->allowedTags)
            ->remove('uid')
            ->get();
        $product = $this->loadModel('file')->processImgURL($product, $this->config->product->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_PRODUCT)->data($product)->autoCheck()
            ->batchCheck($this->config->product->edit->requiredFields, 'notempty')
            ->checkIF(strlen($product->code) == 0, 'code', 'notempty') //the value of product code can be 0 or 00.0
            ->check('name', 'unique', "id != $productID and deleted = '0'")
            ->check('code', 'unique', "id != $productID and deleted = '0'")
            ->where('id')->eq($productID)
            ->exec();
        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $productID, 'product');
            if($product->acl != 'open') $this->loadModel('user')->updateUserView($productID, 'product');
            return common::createChanges($oldProduct, $product);
        }
    }

    /**
     * Batch update products.
     *
     * @access public
     * @return void
     */
    public function batchUpdate()
    {
        $products    = array();
        $allChanges  = array();
        $data        = fixer::input('post')->get();
        $oldProducts = $this->getByIdList($this->post->productIDList);
        foreach($data->productIDList as $productID)
        {
            $productID = (int)$productID;
            $products[$productID] = new stdClass();
            $products[$productID]->name   = $data->names[$productID];
            $products[$productID]->code   = $data->codes[$productID];
            $products[$productID]->PO     = $data->POs[$productID];
            $products[$productID]->QD     = $data->QDs[$productID];
            $products[$productID]->RD     = $data->RDs[$productID];
            $products[$productID]->type   = $data->types[$productID];
            $products[$productID]->line   = $data->lines[$productID];
            $products[$productID]->status = $data->statuses[$productID];
            $products[$productID]->desc   = strip_tags($this->post->descs[$productID], $this->config->allowedTags);
            $products[$productID]->order  = $data->orders[$productID];
        }

        foreach($products as $productID => $product)
        {
            $oldProduct = $oldProducts[$productID];
            $this->dao->update(TABLE_PRODUCT)
                ->data($product)
                ->autoCheck()
                ->batchCheck($this->config->product->edit->requiredFields , 'notempty')
                ->checkIF(strlen($product->code) == 0, 'code', 'notempty') //the value of product code can be 0 or 00.0
                ->check('name', 'unique', "id != $productID and deleted = '0'")
                ->check('code', 'unique', "id != $productID and deleted = '0'")
                ->where('id')->eq($productID)
                ->exec();
            if(dao::isError()) die(js::error('product#' . $productID . dao::getError(true)));
            $allChanges[$productID] = common::createChanges($oldProduct, $product);
        }
        $this->fixOrder();
        return $allChanges;
    }

    /**
     * Close product.
     *
     * @param  int    $productID.
     * @access public
     * @return void
     */
    public function close($productID)
    {
        $oldProduct = $this->getById($productID);
        $now        = helper::now();
        $product= fixer::input('post')
            ->setDefault('status', 'closed')
            ->remove('comment')->get();

        $this->dao->update(TABLE_PRODUCT)->data($product)
            ->autoCheck()
            ->where('id')->eq((int)$productID)
            ->exec();

        if(!dao::isError()) return common::createChanges($oldProduct, $product);
    }

    /**
     * Get stories.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $browseType
     * @param  int    $queryID
     * @param  int    $moduleID
     * @param  string $type requirement|story
     * @param  string $sort
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getStories($productID, $branch, $browseType, $queryID, $moduleID, $type = 'story', $sort, $pager)
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getStories();

        $this->loadModel('story');

        /* Set modules and browse type. */
        $modules    = $moduleID ? $this->loadModel('tree')->getAllChildID($moduleID) : '0';

        $browseType = $browseType == 'bybranch' ? 'bymodule' : $browseType;
        $browseType = ($browseType == 'bymodule' and $this->session->storyBrowseType and $this->session->storyBrowseType != 'bysearch') ? $this->session->storyBrowseType : $browseType;

        /* Get stories by browseType. */
        $stories = array();
        if($browseType == 'unclosed')
        {
            $unclosedStatus = $this->lang->story->statusList;
            unset($unclosedStatus['closed']);
            $stories = $this->story->getProductStories($productID, $branch, $modules, array_keys($unclosedStatus), $type, $sort, true, '', $pager);
        }
        if($browseType == 'unplan')       $stories = $this->story->getByPlan($productID, $queryID, $modules, '', $type, $sort, $pager);
        if($browseType == 'allstory')     $stories = $this->story->getProductStories($productID, $branch, $modules, 'all', $type, $sort, true, '', $pager);
        if($browseType == 'bymodule')     $stories = $this->story->getProductStories($productID, $branch, $modules, 'all', $type, $sort, true, '', $pager);
        if($browseType == 'bysearch')     $stories = $this->story->getBySearch($productID, $branch, $queryID, $sort, '', $type, '', $pager);
        if($browseType == 'assignedtome') $stories = $this->story->getByAssignedTo($productID, $branch, $modules, $this->app->user->account, $type, $sort, $pager);
        if($browseType == 'openedbyme')   $stories = $this->story->getByOpenedBy($productID, $branch, $modules, $this->app->user->account, $type, $sort, $pager);
        if($browseType == 'reviewedbyme') $stories = $this->story->getByReviewedBy($productID, $branch, $modules, $this->app->user->account, $type, $sort, $pager);
        if($browseType == 'closedbyme')   $stories = $this->story->getByClosedBy($productID, $branch, $modules, $this->app->user->account, $type, $sort, $pager);
        if($browseType == 'draftstory')   $stories = $this->story->getByStatus($productID, $branch, $modules, 'draft', $type, $sort, $pager);
        if($browseType == 'activestory')  $stories = $this->story->getByStatus($productID, $branch, $modules, 'active', $type, $sort, $pager);
        if($browseType == 'changedstory') $stories = $this->story->getByStatus($productID, $branch, $modules, 'changed', $type, $sort, $pager);
        if($browseType == 'willclose')    $stories = $this->story->get2BeClosed($productID, $branch, $modules, $type, $sort, $pager);
        if($browseType == 'closedstory')  $stories = $this->story->getByStatus($productID, $branch, $modules, 'closed', $type, $sort, $pager);
        if($browseType == 'emptysr')      $stories = $this->story->getEmptySR($productID, $branch, $modules, '', $type, $sort, $pager);

        return $stories;
    }

    /**
     * Batch get story stage.
     *
     * @param  array  $stories.
     * @access public
     * @return array
     */
    public function batchGetStoryStage($stories)
    {
        /* Set story id list. */
        $storyIdList = array();
        foreach($stories as $story) $storyIdList[$story->id] = $story->id;

        return $this->loadModel('story')->batchGetStoryStage($storyIdList);
    }

    /**
     * Build search form.
     *
     * @param  int    $productID
     * @param  array  $products
     * @param  int    $queryID
     * @param  int    $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($productID, $products, $queryID, $actionURL)
    {
        $this->config->product->search['actionURL'] = $actionURL;
        $this->config->product->search['queryID']   = $queryID;
        $this->config->product->search['params']['plan']['values']    = $this->loadModel('productplan')->getPairs($productID);
        $this->config->product->search['params']['product']['values'] = array($productID => $products[$productID], 'all' => $this->lang->product->allProduct);
        $this->config->product->search['params']['module']['values']  = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'story', $startModuleID = 0);
        if($this->session->currentProductType == 'normal')
        {
            unset($this->config->product->search['fields']['branch']);
            unset($this->config->product->search['params']['branch']);
        }
        else
        {
            $this->config->product->search['fields']['branch'] = $this->lang->product->branch;
            $this->config->product->search['params']['branch']['values']  = array('' => '') + $this->loadModel('branch')->getPairs($productID, 'noempty') + array('all' => $this->lang->branch->all);
        }

        $this->loadModel('search')->setSearchParams($this->config->product->search);
    }

    /**
     * Get projects of a product in pairs.
     *
     * @param  int    $productID
     * @param  string $param    all|nodeleted
     * @access public
     * @return array
     */
    public function getProjectPairs($productID, $branch = 0, $param = 'all')
    {
        $projects = array();
        $datas = $this->dao->select('t2.id, t2.name, t2.deleted')->from(TABLE_PROJECTPRODUCT)
            ->alias('t1')->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq((int)$productID)
            ->beginIF($branch)->andWhere('t1.branch')->in($branch)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->projects)->fi()
            ->andWhere('t2.deleted')->eq(0)
            ->orderBy('t1.project desc')
            ->fetchAll();

        foreach($datas as $data)
        {
            if($param == 'nodeleted' and $data->deleted) continue;
            $projects[$data->id] = $data->name;
        }
        $projects = array('' => '') +  $projects;
        return $projects;
    }

    /**
     * Get roadmap of a proejct
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $count
     * @access public
     * @return array
     */
    public function getRoadmap($productID, $branch = 0, $count = 0)
    {
        $plans    = $this->loadModel('productplan')->getList($productID, $branch);
        $releases = $this->loadModel('release')->getList($productID, $branch);
        $roadmap  = array();
        $total    = 0;

        $parents      = array();
        $orderedPlans = array();
        foreach($plans as $planID => $plan)
        {
            if($plan->parent == '-1')
            {
                $parents[$planID] = $plan->title;
                unset($plans[$planID]);
                continue;
            }
            if(($plan->end != '0000-00-00' and strtotime($plan->end) - time() <= 0) or $plan->end == '2030-01-01') continue;
            $orderedPlans[$plan->end][] = $plan;
        }

        krsort($orderedPlans);
        foreach($orderedPlans as $plans)
        {
            krsort($plans);
            foreach($plans as $plan)
            {
                if($plan->parent > 0 and isset($parents[$plan->parent])) $plan->title = $parents[$plan->parent] . ' / ' . $plan->title;

                $year = substr($plan->end, 0, 4);
                $roadmap[$year][$plan->branch][] = $plan;
                $total++;

                if($count > 0 and $total >= $count) return $this->processRoadmap($roadmap);
            }
        }

        $orderedReleases = array();
        foreach($releases as $release) $orderedReleases[$release->date][] = $release;

        krsort($orderedReleases);
        foreach($orderedReleases as $releases)
        {
            krsort($releases);
            foreach($releases as $release)
            {
                $year = substr($release->date, 0, 4);
                $roadmap[$year][$release->branch][] = $release;
                $total++;

                if($count > 0 and $total >= $count) return $this->processRoadmap($roadmap);
            }
        }

        if($count > 0) return $this->processRoadmap($roadmap);

        $groupRoadmap = array();
        foreach($roadmap as $year => $branchRoadmaps)
        {
            foreach($branchRoadmaps as $branch => $roadmaps)
            {
                $totalData = count($roadmaps);
                $rows      = ceil($totalData / 8);
                $maxPerRow = ceil($totalData / $rows);

                $groupRoadmap[$year][$branch] = array_chunk($roadmaps, $maxPerRow);
                foreach($groupRoadmap[$year][$branch] as $row => $rowRoadmaps) krsort($groupRoadmap[$year][$branch][$row]);
            }
        }

        /* Get last 5 roadmap. */
        $lastKeys    = array_slice(array_keys($groupRoadmap), 0, 5);
        $lastRoadmap = array();
        $lastRoadmap['total'] = 0;
        foreach($lastKeys as $key)
        {
            if($key == '2030')
            {
                $lastRoadmap[$this->lang->productplan->future] = $groupRoadmap[$key];
            }
            else
            {
                $lastRoadmap[$key] = $groupRoadmap[$key];
            }

            foreach($groupRoadmap[$key] as $branchRoadmaps) $lastRoadmap['total'] += (count($branchRoadmaps, 1) - count($branchRoadmaps));
        }

        return $lastRoadmap;
    }

    /**
     * Process roadmap.
     *
     * @param  array  $roadmap
     * @access public
     * @return array
     */
    public function processRoadmap($roadmapGroups)
    {
        $newRoadmap = array();
        foreach($roadmapGroups as $year => $branchRoadmaps)
        {
            foreach($branchRoadmaps as $branch => $roadmaps)
            {
                foreach($roadmaps as $roadmap) $newRoadmap[] = $roadmap;
            }
        }
        krsort($newRoadmap);
        return $newRoadmap;
    }

    /**
     * Get team members of a product from projects.
     *
     * @param  object   $product
     * @access public
     * @return array
     */
    public function getTeamMemberPairs($product)
    {
        $members[$product->PO] = $product->PO;
        $members[$product->QD] = $product->QD;
        $members[$product->RD] = $product->RD;
        $members[$product->createdBy] = $product->createdBy;

        /* Set projects and teams as static thus we can only query sql one times. */
        static $projects, $teams;
        if(empty($projects))
        {
            $projects = $this->dao->select('t1.project, t1.product')->from(TABLE_PROJECTPRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                ->where('t2.deleted')->eq(0)
                ->fetchGroup('product', 'project');
        }
        if(empty($teams))
        {
            $teams = $this->dao->select('t1.root, t1.account')->from(TABLE_TEAM)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.root = t2.id')
                ->where('t2.deleted')->eq(0)
                ->andWhere('t1.type')->eq('project')
                ->fetchGroup('root', 'account');
        }

        if(!isset($projects[$product->id])) return $members;
        $productProjects = $projects[$product->id];

        $projectTeams = array();
        foreach(array_keys($productProjects) as $projectID) $projectTeams = array_merge($projectTeams, array_keys($teams[$projectID]));

        return array_flip(array_merge($members, $projectTeams));
    }

    /**
     * Get product stat by id
     *
     * @param  int    $productID
     * @param  string $storyType
     * @access public
     * @return object|bool
     */
    public function getStatByID($productID, $storyType = 'story')
    {
        if(!$this->checkPriv($productID)) return false;
        $product = $this->getById($productID);
        $stories = $this->dao->select('product, status, count(status) AS count')->from(TABLE_STORY)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq($storyType)
            ->andWhere('product')->eq($productID)
            ->groupBy('product, status')->fetchAll('status');
        /* Padding the stories to sure all status have records. */
        foreach(array_keys($this->lang->story->statusList) as $status)
        {
            $stories[$status] = isset($stories[$status]) ? $stories[$status]->count : 0;
        }

        $plans    = $this->dao->select('count(*) AS count')->from(TABLE_PRODUCTPLAN)->where('deleted')->eq(0)->andWhere('product')->eq($productID)->andWhere('end')->gt(helper::now())->fetch();
        $builds   = $this->dao->select('count(*) AS count')->from(TABLE_BUILD)->where('deleted')->eq(0)->andWhere('product')->eq($productID)->fetch();
        $cases    = $this->dao->select('count(*) AS count')->from(TABLE_CASE)->where('deleted')->eq(0)->andWhere('product')->eq($productID)->fetch();
        $bugs     = $this->dao->select('count(*) AS count')->from(TABLE_BUG)->where('deleted')->eq(0)->andWhere('product')->eq($productID)->fetch();
        $docs     = $this->dao->select('count(*) AS count')->from(TABLE_DOC)->where('deleted')->eq(0)->andWhere('product')->eq($productID)->fetch();
        $releases = $this->dao->select('count(*) AS count')->from(TABLE_RELEASE)->where('deleted')->eq(0)->andWhere('product')->eq($productID)->fetch();
        $projects = $this->dao->select('count("t1.*") AS count')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t2.deleted')->eq(0)
            ->andWhere('t1.product')->eq($productID)
            ->fetch();

        $product->stories  = $stories;
        $product->plans    = $plans    ? $plans->count : 0;
        $product->releases = $releases ? $releases->count : 0;
        $product->builds   = $builds   ? $builds->count : 0;
        $product->cases    = $cases    ? $cases->count : 0;
        $product->projects = $projects ? $projects->count : 0;
        $product->bugs     = $bugs     ? $bugs->count : 0;
        $product->docs     = $docs     ? $docs->count : 0;

        return $product;
    }

    /**
     * Get product stats.
     *
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $status
     * @param  int    $line
     * @param  string $storyType requirement|story
     * @access public
     * @return array
     */
    public function getStats($orderBy = 'order_desc', $pager = null, $status = 'noclosed', $line = 0, $storyType = 'story')
    {
        $this->loadModel('report');
        $this->loadModel('story');
        $this->loadModel('bug');

        $products = $this->getList($status, $limit = 0, $line);
        $products = $this->dao->select('*')->from(TABLE_PRODUCT)
            ->where('id')->in(array_keys($products))
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $stories = $this->dao->select('product, status, count(status) AS count')
            ->from(TABLE_STORY)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq($storyType)
            ->andWhere('product')->in(array_keys($products))
            ->groupBy('product, status')
            ->fetchGroup('product', 'status');

        /* Padding the stories to sure all products have records. */
        $emptyStory = array_keys($this->lang->story->statusList);
        foreach(array_keys($products) as $productID)
        {
            if(!isset($stories[$productID])) $stories[$productID] = $emptyStory;
        }

        /* Padding the stories to sure all status have records. */
        foreach($stories as $key => $story)
        {
            foreach(array_keys($this->lang->story->statusList) as $status)
            {
                $story[$status] = isset($story[$status]) ? $story[$status]->count : 0;
            }
            $stories[$key] = $story;
        }

        $plans = $this->dao->select('product, count(*) AS count')
            ->from(TABLE_PRODUCTPLAN)
            ->where('deleted')->eq(0)
            ->andWhere('product')->in(array_keys($products))
            ->andWhere('end')->gt(helper::now())
            ->groupBy('product')
            ->fetchPairs();

        $releases = $this->dao->select('product, count(*) AS count')
            ->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('product')->in(array_keys($products))
            ->groupBy('product')
            ->fetchPairs();

        $bugs = $this->dao->select('product,count(*) AS conut')
            ->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->andWhere('product')->in(array_keys($products))
            ->groupBy('product')
            ->fetchPairs();
        $unResolved = $this->dao->select('product,count(*) AS count')
            ->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->andwhere('status')->eq('active')
            ->andWhere('product')->in(array_keys($products))
            ->groupBy('product')
            ->fetchPairs();
        $assignToNull = $this->dao->select('product,count(*) AS count')
            ->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->andwhere('assignedTo')->eq('')
            ->andWhere('product')->in(array_keys($products))
            ->groupBy('product')
            ->fetchPairs();

        $stats = array();
        foreach($products as $key => $product)
        {
            $product->stories  = $stories[$product->id];
            $product->plans    = isset($plans[$product->id])    ? $plans[$product->id]    : 0;
            $product->releases = isset($releases[$product->id]) ? $releases[$product->id] : 0;

            $product->bugs         = isset($bugs[$product->id]) ? $bugs[$product->id] : 0;
            $product->unResolved   = isset($unResolved[$product->id]) ? $unResolved[$product->id] : 0;
            $product->assignToNull = isset($assignToNull[$product->id]) ? $assignToNull[$product->id] : 0;
            $stats[] = $product;
        }

        return $stats;
    }

    /**
     * Get the summary of product's stories.
     *
     * @param  array    $stories
     * @param  string   $storyType  story|requirement
     * @access public
     * @return string.
     */
    public function summary($objects, $storyType = 'contract')
    {
        
        $common = $storyType=='contract'?$this->lang->contract->common:$this->lang->invoice->common;
        return sprintf($storyType=='contract'?$this->lang->contract->contractSummary:$this->lang->invoice->contractSummary, count($objects),  $common);
    }

    /**
     * Judge an action is clickable or not.
     *
     * @param  object $product
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($product, $action)
    {
        $action = strtolower($action);

        if($action == 'close') return $product->status != 'closed';

        return true;
    }

    /**
     * Create the link from module,method,extra
     *
     * @param  string  $module
     * @param  string  $method
     * @param  mix     $extra
     * @access public
     * @return void
     */
    public function getProductLink($module, $method, $extra, $branch = false)
    {
        $link = '';
        if(strpos('product,roadmap,bug,testcase,testtask,story,qa,testsuite,testreport,build', $module) !== false)
        {
            if($module == 'product' && $method == 'project')
            {
                $link = helper::createLink($module, $method, "status=all&productID=%s" . ($branch ? "&branch=%s" : ''));
            }
            elseif($module == 'product' && ($method == 'dynamic' or $method == 'doc' or $method == 'view'))
            {
                $link = helper::createLink($module, $method, "productID=%s");
            }
            elseif($module == 'qa' && $method == 'index')
            {
                $link = helper::createLink('bug', 'browse', "productID=%s" . ($branch ? "&branch=%s" : ''));
            }
            elseif($module == 'product' && ($method == 'browse' or $method == 'index' or $method == 'all'))
            {
                $link = helper::createLink($module, 'browse', "productID=%s" . ($branch ? "&branch=%s" : ''));
            }
            else
            {
                $link = helper::createLink($module, $method, "productID=%s" . ($branch ? "&branch=%s" : ''));
            }
        }else if($module == 'contract' && ($method=='invoiceList'||$method=="createinvoice") ){
            // list view index
        }else if($module == 'contract' && $method=="createinvoice"){
            $link = helper::createLink($module, "browse", "contractID=%s");
        }
        else if($module == 'contract' ){
            $link = helper::createLink($module, $method, "contractID=%s");
        }

        return $link;
    }

    /**
     * Fix order.
     *
     * @access public
     * @return void
     */
    public function fixOrder()
    {
        $products = $this->dao->select('id,`order`')->from(TABLE_PRODUCT)->orderBy('order')->fetchPairs('id', 'order');

        $i = 0;
        foreach($products as $id => $order)
        {
            $i++;
            $newOrder = $i * 5;
            if($order == $newOrder) continue;
            $this->dao->update(TABLE_PRODUCT)->set('`order`')->eq($newOrder)->where('id')->eq($id)->exec();
        }
    }

    /**
     * get the latest project of the product.
     *
     * @param  int     $productID
     * @access public
     * @return object
     */
    public function getLatestProject($productID)
    {
        return $this->dao->select('t2.id, t2.name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq((int)$productID)
            ->andWhere('t2.status')->ne('closed')
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy('t2.begin desc')
            ->limit(1)
            ->fetch();
    }
    public function submit($invoiceID,$contractID){
        $approval=$this->dao->select("*")->from("zt_approval")->where('objectType')->eq('contract')->andWhere('objectID')->eq($contractID."order by  `order`")->fetchALL();
        $userList=array();
        foreach($approval as $ap){//create approve list
            $object['objectType']='invoice';
            $object['objectID']=$invoiceID;
            $object['user']=$ap->user;
            $object['sign']=$ap->sign;
            $object['order']=$ap->order;
            if($ap->order=='1'){
               array_push($userList,$ap->user);
            }
            $object['status']=$ap->status;
            $this->dao->insert('zt_approval')->data($object)->exec();
        }
        $this->sendApproveNote($invoiceID,$userList);


    }



    public function sendApproveNote($invoiceID,$userList)
    {
        $this->loadModel('mail');
        $invoice  = $this->getById($invoiceID);

        $oldcwd     = getcwd(); // system call

        $modulePath = $this->app->getModulePath($appName = '', 'contract');
        $viewFile   = $modulePath . 'view/sendapprovenote.html.php';// email content
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendapprovenote.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendapprovenote.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        $mailContent = ob_get_contents();
        ob_end_clean();

        chdir($oldcwd);

        $subject = " invoice #$invoice->id is ready for authorization";
        // Send emails. 
	foreach($userList as $value){
        	$this->mail->send($value, $subject, $mailContent,$cclist='',$includeMe=false);
        }
	if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
	echo 'sent';
    }
    public function notifyCM($invoiceID)
    {
        $this->loadModel('mail');
        $invoice  = $this->getById($invoiceID);
	$contract=$this->getContractByID($invoice->contractID);
	$userList=json_decode($contract->contractManager,true);
        $oldcwd     = getcwd(); // system call
        //$modulePath = $this->app->getModulePath($appName = '', 'contract');
        //$viewFile   = $modulePath . 'view/sendapprovenote.html.php';// email content
        //chdir($modulePath . 'view');

       // if(file_exists($modulePath . 'ext/view/sendapprovenote.html.php'))
        //{
        //    $viewFile = $modulePath . 'ext/view/sendapprovenote.html.php';
       //     chdir($modulePath . 'ext/view');
       // }

        ob_start();
        //include $viewFile;
	echo "testing";
        $mailContent = ob_get_contents();
        ob_end_clean();

        chdir($oldcwd);
        $subject = " invoice #$invoice->id is updated";
        // Send emails. 
	foreach($userList as $value){
        	$this->mail->send($value, $subject, $mailContent,$cclist='',$includeMe=false);
        }
	if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
	echo 'sent';
    }

    /** 2022.1.13
     * Get invoice stat by id
     *
     * @param  int    $productID
     * @param  string $storyType
     * @access public
     * @return object|bool
     */
    public function getInvoiceStatByID($invoiceID)
    {
        if(!$this->checkPriv($invoiceID)) {
	return false;
	}
        $invoice = $this->getById($invoiceID);
    

        $invoice->id     = $invoiceID;
        $invoice->refNo  = $this->dao->select('refNo')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('refNo');
        $invoice->status = $this->dao->select('status')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('status');
        $invoice->amount = $this->dao->select('amount')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('amount');
        $invoice->submitdate = //isset($submitteddate[$invoice->id])? $submitteddate[$invoice->id]: 'Not determined'
                               $this->dao->select('submitdate')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('submitdate');
        $invoice->step = $this->dao->select('step')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('step');
        $invoice->contractID = $this->dao->select('contractID')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('contractID');
        $invoice->destription = $this->dao->select('description')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('description');

        $invoice->item = $this->dao->select('item')->from('zt_invoicedetails')->Where('invoiceID')->eq($invoiceID)->fetchPairs('item');
        $invoice->price = $this->dao->select('price')->from('zt_invoicedetails')->Where('invoiceID')->eq($invoiceID)->fetchPairs('price');
      

        return $invoice;
    }
    public function getInvoiceList($status = 'all', $limit = 0, $line = 0)
    {
        return $this->dao->select('*')->from('zt_invoice')
            ->where('deleted')->eq(0)
            ->beginIF($limit > 0)->limit($limit)->fi()
            ->fetchAll('id');
    }
    
    public function getInvoiceStats($contractID, $orderBy = 'order_desc', $pager = null, $line = 0, $storyType = 'story')
    {
        $this->loadModel('report');
        $this->loadModel('story');
        $this->loadModel('bug');

        $invoices = $this->getInvoiceList($status, $limit = 0, $line); 
        /*$invoice = $this->dao->select('*')->from('zt_invoice')
            ->where('id')->in(array_keys($invoices))
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');*/
            

        //$plans = $this->dao->select('product, count(*) AS count')
            //->from(TABLE_PRODUCTPLAN)
            //->where('deleted')->eq(0)
            //->andWhere('product')->in(array_keys($products))
            //->andWhere('end')->gt(helper::now())
            //->groupBy('product')
            //->fetchPairs();

        //$releases = $this->dao->select('product, count(*) AS count')
            //->from(TABLE_RELEASE)
            //->where('deleted')->eq(0)
            //->andWhere('product')->in(array_keys($products))
            //->groupBy('product')
            //->fetchPairs();

       // $bugs = $this->dao->select('product,count(*) AS conut')
            //->from(TABLE_BUG)
            //->where('deleted')->eq(0)
            //->andWhere('product')->in(array_keys($products))
            //->groupBy('product')
            //->fetchPairs();
        //$unResolved = $this->dao->select('product,count(*) AS count')
            //->from(TABLE_BUG)
            //->where('deleted')->eq(0)
            //->andwhere('status')->eq('active')
            //->andWhere('product')->in(array_keys($products))
            //->groupBy('product') 
            //->fetchPairs();
        //$assignToNull = $this->dao->select('product,count(*) AS count')
            //->from(TABLE_BUG)
            //->where('deleted')->eq(0)
            //->andwhere('assignedTo')->eq('')
            //->andWhere('product')->in(array_keys($products))
            //->groupBy('product')
            //->fetchPairs();

        
        $invoiceID = $this->dao->select('id')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('contractID')->eq($contractID)->fetch('');
       
        $stats = array();

        foreach($invoices as $invoiceID => $invoice)
        { 
            
            $invoiceContractID = $this->dao->select('contractID')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('contractID');

            if($contractID == $invoiceContractID){
            $invoice->refNo  = $this->dao->select('refNo')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('refNo');
            $invoice->status = $this->dao->select('status')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('status');
            $invoice->amount = $this->dao->select('amount')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('amount');
            $invoice->submitdate = //isset($submitteddate[$invoice->id])? $submitteddate[$invoice->id]: 'Not determined'
                                      $this->dao->select('submitdate')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('submitdate');
            $invoice->step = $this->dao->select('step')->from('zt_invoice')->where('deleted')->eq(0)->andWhere('id')->eq($invoiceID)->fetch('step');
            $invoice->contractName = $this->dao->select('contractName')->from('zt_contract')->where('deleted')->eq(0)->andWhere('id')->eq($invoice->contractID)->fetch('contractName');
        
            //$product->stories  = $stories[$product->id];
            //$product->plans    = isset($plans[$product->id])    ? $plans[$product->id]    : 0;
            //$product->releases = isset($releases[$product->id]) ? $releases[$product->id] : 0;

            //$product->bugs         = isset($bugs[$product->id]) ? $bugs[$product->id] : 0;
            //$product->unResolved   = isset($unResolved[$product->id]) ? $unResolved[$product->id] : 0;
            //$product->assignToNull = isset($assignToNull[$product->id]) ? $assignToNull[$product->id] : 0;
            $stats[] = $invoice;
            }
        }
  
        return $stats;
    }

    public function getApprovalList($id,$type='invoice',$step='0'){
        $approvals=$this->dao->select('*')->from('zt_approval')->where("objectType")->eq($type)->andWhere("objectID")->eq($id)->andWhere('`order`')->eq($step)->andWhere('status')->eq('waiting')->fetchAll();
        return $approvals;
    }
    public function getApprovalLists($status = 'all', $limit = 0, $line = 0)
    {
        return $this->dao->select('*')->from('zt_approval')
            ->beginIF($limit > 0)->limit($limit)->fi()
            ->fetchAll('id');
    }

    public function updateInvoice($invoiceID){
        $invoice=$this->dao->select('*')->from('zt_invoice')->where("id")->eq($invoiceID)->fetch();
        $invoice->description=$this->post->description;
        $invoice->refNo=$this->post->refNo;
        $invoice->amount=$this->post->amount;
        $invoice->lastEdit=helper::now();

        $this->dao->update('zt_invoice')->data($invoice)->where('id')->eq($invoiceID)->exec();// update invoice record

        $this->dao->delete()->from('zt_invoicedetails')->where('invoiceID')->eq($invoiceID)->exec();// update invoice details record
        $i=0;
        for($i;$i<count($_POST['item']);$i++){//create invoice details
            if($_POST['item'][$i]=="" || empty($_POST['item'][$i]) || !isset($_POST['item'][$i])){
                continue;
            }else{
                $details['invoiceID']=$invoiceID;
                $details['item']=$_POST['item'][$i];
                $details['price']=isset($_POST['price'][$i])?$_POST['price'][$i]:'0';
                $this->dao->insert("zt_invoicedetails")->data($details)->exec();
            }
        }
        
        
        return true;

    }
    public function payment($invoiceID){
        $invoice=$this->dao->select('*')->from('zt_invoice')->where("id")->eq($invoiceID)->fetch();
        $invoice->status='paid';
        $invoice->paymentNo=$this->post->paymentNo;
        $invoice->lastEdit=helper::now();
        $this->dao->update('zt_invoice')->data($invoice)->where('id')->eq($invoiceID)->exec();// update invoice record
        return true;
    }
      /** 2022.1.13
     * Get approval stats.
     *
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $status
     * @param  int    $line
     * @param  string $storyType requirement|story
     * @access public
     * @return array
     */
    public function getApprovalStats($invoiceID, $orderBy = 'order_desc', $pager = null, $line = 0, $storyType = 'story')
    {


       // $approvals = $this->getApprovalLists($status, $limit = 0, $line); 
        $approvalID = $this->dao->select('*')->from('zt_approval')->where('objectID')->eq($invoiceID)->andWhere('objectType')->eq('invoice')->orderBy('`order`')->fetchALL('id');
        $stats = array();

        foreach( $approvalID as $approval)
        { 
            $stats[] = $approval;
        }

        return $stats;
    }
    public function printCell($col, $contract, $users)
    {
        $canBatchEdit         = common::hasPriv('contract', 'batchEdit');
        $canBatchAction       = ($canBatchEdit);

        $canView   = common::hasPriv('contract', 'view');
        $storyLink = helper::createLink('contract', 'view', "contract=$contract->id");
        $account   = $this->app->user->account;
        $id        = $col->id;
        if($col->show)
        {
            $class = "c-{$id}";
            $title = '';
            $style = '';

            if($id == 'openedBy')
            {
                $title = zget($users, $story->openedBy, $story->openedBy);
            }elseif($id == 'title')
            {
                $title = $contract->title;
            }

            echo "<td class='" . $class . "' title='$title' style='$style'>";
           // if(isset($this->config->bizVersion)) $this->loadModel('flow')->printFlowCell('contract', $contract, $id);
            switch($id)
            {
            case 'id':
                if($canBatchAction)
                {
                    echo html::checkbox('contractIdList', array($contract->id => '')) . html::a(helper::createLink('contract', 'view', "storyID=$contract->id") , sprintf('%03d', $contract->id));
                }
                else
                {
                    printf('%03d', $contract->id);
                }
                break;
            case 'pri':
                echo "<span class='label-pri label-pri-" . $story->pri . "' title='" . zget($this->lang->story->priList, $story->pri, $story->pri) . "'>";
                echo zget($this->lang->story->priList, $story->pri, $story->pri);
                echo "</span>";
                break;
            case 'title':
                echo  html::a($storyLink, $contract->contractName, '', "style='color: $contract->color'");
                break;
            case 'RefNo':
                echo   $contract->refNo;
                break;
            case 'plan':
                echo isset($story->planTitle) ? $story->planTitle : '';
                break;
            case 'branch':
                echo zget($branches, $story->branch, '');
                break;
            case 'keywords':
                echo $story->keywords;
                break;
            case 'source':
                echo zget($this->lang->story->sourceList, $story->source, $story->source);
                break;
            case 'sourceNote':
                echo $story->sourceNote;
                break;
            case 'status':
                echo "<span class='status-{$contract->status}'>";
                echo $contract->status;
                echo '</span>';
                break;
            case 'estimate':
                echo $story->estimate;
                break;
            case 'stage':
                if(isset($storyStages[$story->id]) and !empty($branches))
                {
                    echo "<div class='dropdown dropdown-hover'>";
                    echo $this->lang->story->stageList[$story->stage];
                    echo "<span class='caret'></span>";
                    echo "<ul class='dropdown-menu pull-right'>";
                    foreach($storyStages[$story->id] as $storyBranch => $storyStage)
                    {
                        if(isset($branches[$storyBranch])) echo '<li class="text-ellipsis">' . $branches[$storyBranch] . ": " . $this->lang->story->stageList[$storyStage->stage] . '</li>';
                    }
                    echo "</ul>";
                    echo '</div>';
                }
                else
                {
                    echo $this->lang->story->stageList[$story->stage];
                }
                break;
            case 'taskCount':
                $tasksLink = helper::createLink('story', 'tasks', "storyID=$story->id");
                $storyTasks[$story->id] > 0 ? print(html::a($tasksLink, $storyTasks[$story->id], '', 'class="iframe"')) : print(0);
                break;
            case 'bugCount':
                $bugsLink = helper::createLink('story', 'bugs', "storyID=$story->id");
                $storyBugs[$story->id] > 0 ? print(html::a($bugsLink, $storyBugs[$story->id], '', 'class="iframe"')) : print(0);
                break;
            case 'invoiceCount':
                $invoiceLink = helper::createLink('contract', 'invoiceList', "contract=$contract->id");
                //$storyCases[$story->id] > 0 ? print(html::a($casesLink, $storyCases[$story->id], '', 'class="iframe"')) : print(0);
                //get invoice count by contract(i.e. submited)
                break;
            case 'openedBy':
                echo zget($users, $story->openedBy, $story->openedBy);
                break;
            case 'openedDate':
                echo substr($story->openedDate, 5, 11);
                break;
            case 'assignedTo':
                $this->printAssignedHtml($story, $users);
                break;
            case 'assignedDate':
                echo substr($story->assignedDate, 5, 11);
                break;
            case 'reviewedBy':
                echo $story->reviewedBy;
                break;
            case 'reviewedDate':
                echo substr($story->reviewedDate, 5, 11);
                break;
            case 'closedBy':
                echo zget($users, $story->closedBy, $story->closedBy);
                break;
            case 'closedDate':
                echo substr($story->closedDate, 5, 11);
                break;
            case 'closedReason':
                echo zget($this->lang->story->reasonList, $story->closedReason, $story->closedReason);
                break;
            case 'lastEditedBy':
                echo zget($users, $story->lastEditedBy, $story->lastEditedBy);
                break;
            case 'lastEditedDate':
                echo substr($story->lastEditedDate, 5, 11);
                break;
            case 'mailto':
                $mailto = explode(',', $story->mailto);
                foreach($mailto as $account)
                {
                    $account = trim($account);
                    if(empty($account)) continue;
                    echo zget($users, $account) . ' &nbsp;';
                }
                break;
            case 'version':
                echo $story->version;
                break;
            case 'appointedParty':
                echo $contract->appointedParty;
                break;
            case 'contractManager':
		$cm=json_decode($contract->contractManager,true);
		if($cm!=NULL){
	                foreach(json_decode($contract->contractManager,true) as $people){
				echo "$people,";
			}
		}else{
			echo $contract->contractManager;
		}
		break;
            case 'amount':
                echo $contract->amount;
                break;
            case 'actions':
                $vars = "story={$contract->id}";
                if($this->app->user->account==$contract->contractManager || $this->app->user->account=="admin"){
                    common::printIcon('contract', 'edit',$vars, $contract, 'list');
                    common::printIcon('contract', 'finish',$vars, $contract, 'list', '', '', 'iframe', true);
                }
                common::printIcon('contract', 'invoiceList',$vars,  $contract, 'list');

                if($this->app->user->account==$contract->appointedParty || $this->app->user->account=="admin"){
                    common::printIcon('contract', 'createInvoice',$vars,  $contract, 'list');
                }
                break;
            }
            echo '</td>';
        }
    }

}

