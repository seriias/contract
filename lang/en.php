<?php
/**
 * The product module English file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: en.php 5091 2013-07-10 06:06:46Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
$lang->product->common       = $lang->productCommon;
$lang->product->index        = $lang->productCommon . ' Home';
$lang->product->browse       = "{$lang->storyCommon} List";
$lang->product->dynamic      = 'Dynamics';
$lang->product->view         = "{$lang->productCommon} Detail";
$lang->product->edit         = "Edit {$lang->productCommon}";
$lang->product->batchEdit    = 'Batch Edit';
$lang->product->create       = "Create {$lang->productCommon}";
$lang->product->delete       = "Delete {$lang->productCommon}";
$lang->product->deleted      = 'Deleted';
$lang->product->close        = "Close";
$lang->product->closeAction  = "Close {$lang->productCommon}";
$lang->product->select       = "Select {$lang->productCommon}";
$lang->product->mine         = 'Mine:';
$lang->product->other        = 'Others:';
$lang->product->closed       = 'Closed';
$lang->product->updateOrder  = 'Order';
$lang->product->orderAction  = "Rank {$lang->productCommon}";
$lang->product->all          = "All {$lang->productCommon}s";
$lang->product->export       = 'Export';
$lang->product->exportAction = "Export {$lang->productCommon}";

$lang->product->basicInfo = 'Basic Info';
$lang->product->otherInfo = 'Other Info';

//added
$lang->product->map          = $lang->mapCommon; //emsd map
$lang->product->location     = 'Location'; //emsd map

$lang->product->plans       = 'Plans';
$lang->product->releases    = 'Releases';
$lang->product->docs        = 'Doc';
$lang->product->bugs        = "Linked {$lang->bugCommon}";
$lang->product->projects    = "Linked {$lang->projectCommon}";
$lang->product->cases       = 'Case';
$lang->product->builds      = 'Build';
$lang->product->roadmap     = "{$lang->productCommon} Roadmap";
$lang->product->doc         = "{$lang->productCommon} Documents";
$lang->product->project     = $lang->projectCommon . ' List';
$lang->product->build       = 'Build List';
$lang->product->projectInfo = "{$lang->projectCommon}s that are linked to this {$lang->productCommon} are listed below.";

$lang->product->currentProject        = "{$lang->projectCommon}";
$lang->product->activeStories         = 'Active [S]';
$lang->product->activeStoriesTitle    = "Active {$lang->storyCommon}";
$lang->product->changedStories        = 'Changed [S]';
$lang->product->changedStoriesTitle   = "Changed {$lang->storyCommon}";
$lang->product->draftStories          = 'Draft [S]';
$lang->product->draftStoriesTitle     = "Draft {$lang->storyCommon}";
$lang->product->closedStories         = 'Closed [S]';
$lang->product->closedStoriesTitle    = "Closed {$lang->storyCommon}";
$lang->product->unResolvedBugs        = 'Active [B]';
$lang->product->unResolvedBugsTitle   = "Active {$lang->bugCommon}";
$lang->product->assignToNullBugs      = 'Unassigned [B]';
$lang->product->assignToNullBugsTitle = "Unassigned {$lang->bugCommon}";

$lang->product->confirmDelete  = " Do you want to delete the {$lang->productCommon}?";
$lang->product->errorNoProduct = "No {$lang->productCommon} is created yet!";
$lang->product->accessDenied   = "You have no access to the {$lang->productCommon}.";

$lang->product->id            = 'ID';
$lang->product->name          = "{$lang->productCommon} Name";
$lang->product->code          = 'Code';
$lang->product->line          = "{$lang->productCommon} Line";
$lang->product->order         = 'Rank';
$lang->product->type          = 'Type';
$lang->product->typeAB        = 'Type';
$lang->product->status        = 'Status';
$lang->product->subStatus     = 'Sub Status';
$lang->product->desc          = 'Description';
$lang->product->manager       = 'Managers';
$lang->product->PO            = "{$lang->productCommon} Owner";
$lang->product->QD            = 'QA Manager';
$lang->product->RD            = 'Release Manager';
$lang->product->acl           = 'Access Control';
$lang->product->whitelist     = 'Whitelist';
$lang->product->branch        = '%s';
$lang->product->qa            = 'Test';
$lang->product->release       = 'Release';
$lang->product->allRelease    = 'All Releases';
$lang->product->maintain      = 'Maintenance';
$lang->product->latestDynamic = 'Dynamics';
$lang->product->plan          = 'Plan';
$lang->product->iteration     = 'Iterations';
$lang->product->iterationInfo = '%s Iteration';
$lang->product->iterationView = 'Detail';
$lang->product->createdBy     = 'CreatedBy';
$lang->product->createdDate   = 'createdDate';

$lang->product->searchStory  = 'Search';
$lang->product->assignedToMe = 'AssignedToMe';
$lang->product->openedByMe   = 'CreatedByMe';
$lang->product->reviewedByMe = 'ReviewedByMe';
$lang->product->closedByMe   = 'ClosedByMe';
$lang->product->draftStory   = 'Draft';
$lang->product->activeStory  = 'Activated';
$lang->product->changedStory = 'Changed';
$lang->product->willClose    = 'ToBeClosed';
$lang->product->closedStory  = 'Closed';
$lang->product->unclosed     = 'Open';
$lang->product->unplan       = 'Unplanned';
$lang->product->viewByUser   = 'By User';

$lang->product->allStory             = 'All ';
$lang->product->allProduct           = 'All';
$lang->product->allProductsOfProject = 'All Linked ' . $lang->productCommon;

$lang->product->typeList['']         = '';
$lang->product->typeList['normal']   = 'Normal';
$lang->product->typeList['branch']   = 'Multi-Branch';
$lang->product->typeList['platform'] = 'Multi-Platform';

$lang->product->typeTips = array();
$lang->product->typeTips['branch']   = ' (for the customized context. e.g. outsourcing teams)';
$lang->product->typeTips['platform'] = ' (for cross-platform applications, e.g. IOS, Android, PC, etc.)';

$lang->product->branchName['normal']   = '';
$lang->product->branchName['branch']   = 'Branch';
$lang->product->branchName['platform'] = 'Platform';

$lang->product->statusList['']       = '';
$lang->product->statusList['normal'] = 'Normal';
$lang->product->statusList['closed'] = 'Closed';

$lang->product->aclList['open']    = "Default (Users with privileges to {$lang->productCommon} can access it.)";
$lang->product->aclList['private'] = "Private {$lang->productCommon} ({$lang->projectCommon} team members only)";
$lang->product->aclList['custom']  = 'Custom (Team members and Whitelist members can access it.)';

$lang->product->storySummary   = "Total <strong>%s</strong> %s on this page. Estimates: <strong>%s</strong> ({$lang->hourCommon}), and Case Coverage: <strong>%s</strong>.";
$lang->product->checkedSummary = "<strong>%total%</strong> %storyCommon% selected, Esitmates: <strong>%estimate%</strong> ({$lang->hourCommon}), and Case Coverage: <strong>%rate%</strong>.";
$lang->product->noModule       = '<div>You have no modules. </div><div>Manage Now</div>';
$lang->product->noProduct      = "No {$lang->productCommon} yet. ";
$lang->product->noMatched      = '"%s" cannot be found.' . $lang->productCommon;

$lang->product->featureBar['browse']['allstory']     = $lang->product->allStory;
$lang->product->featureBar['browse']['unclosed']     = $lang->product->unclosed;
$lang->product->featureBar['browse']['assignedtome'] = $lang->product->assignedToMe;
$lang->product->featureBar['browse']['openedbyme']   = $lang->product->openedByMe;
$lang->product->featureBar['browse']['reviewedbyme'] = $lang->product->reviewedByMe;
$lang->product->featureBar['browse']['draftstory']   = $lang->product->draftStory;
$lang->product->featureBar['browse']['more']         = $lang->more;

$lang->product->featureBar['all']['noclosed'] = $lang->product->unclosed;
$lang->product->featureBar['all']['closed']   = $lang->product->statusList['closed'];
$lang->product->featureBar['all']['all']      = $lang->product->allProduct;

$lang->product->moreSelects['closedbyme']   = $lang->product->closedByMe;
$lang->product->moreSelects['activestory']  = $lang->product->activeStory;
$lang->product->moreSelects['changedstory'] = $lang->product->changedStory;
$lang->product->moreSelects['willclose']    = $lang->product->willClose;
$lang->product->moreSelects['closedstory']  = $lang->product->closedStory;





$lang->invoice->approvallist       ='Approval List';
$lang->invoice->create             ="Create Invoice";
$lang->invoice->acl                = 'Access Control';
$lang->invoice->aclList['open']    = "Default (Users with privileges to {$lang->contract->common} can access it.)";
$lang->invoice->aclList['private'] = "Only who related to the contract can access";
$lang->invoice->aclList['custom']  = 'Custom (Team members and Whitelist members can access it.)';
$lang->invoice->whitelist          = "White List";
$lang->invoice->approve            = "approve";
$lang->invoice->reject             = "reject";
$lang->contract->contractID        = 'Contract ID';
$lang->invoice->status             = 'Status';
$lang->invoice->refNo             = 'Invoice Ref.No.';

$lang->invoice->amount             = 'Total Amount';
$lang->invoice->submitteddate      = 'Submitted date';
$lang->invoice->step               = 'Current Sequence';
$lang->invoice->detail             = 'Details';
$lang->invoice->AccountStatus      = 'AccountStatus';
$lang->invoice->invoiceID          = 'Invoice ID';
$lang->invoice->item               = 'Item';
$lang->invoice->price              = 'Price';
$lang->invoice->edit               = 'Edit';
$lang->invoice->deleteInvoice ="Delete Invoice";
$lang->invoice->editinvoice ="Edit Invoice";
$lang->invoice->basicInfo   ="Basic Info:";
$lang->invoice->softcopy    ="Invoice SoftCopy";
$lang->invoice->eoRef       ="Ref. No.";
$lang->invoice->action      ='Action';



$lang->contract->common ='Contract';
$lang->contract->create ='Create Contract';
$lang->contract->batchcreate ='Batch Create';
$lang->contract->name   ='Contract Name';
$lang->contract->desc   ="Description";
$lang->contract->amount ="Total Amount";
$lang->contract->eoRef  ="Reference Number";
$lang->contract->ap     ="Appointed Party";
$lang->contract->cm     ="Contract Manager";
$lang->contract->ras     ="Required Approvers";
$lang->contract->startDate   ="Start Date";
$lang->contract->endDate     ="End Date";
$lang->contract->dateRange   ='Duration';
$lang->contract->acl           = 'Access Control';
$lang->contract->aclList['open']    = "Default (Users with privileges to {$lang->contract->common} can access it.)";
$lang->contract->aclList['private'] = "Only who related to the contract can access";
$lang->contract->aclList['custom']  = 'Custom (Team members and Whitelist members can access it.)';
$lang->contract->whitelist          = "White List";
$lang->contract->payment          = "Submit Payment";
$lang->contract->paymentNo          = "Payment Number";
$lang->contract->contractSummary   = "Total <strong>%s</strong> %s on this page.";
$lang->contract->checkedSummary = "<strong>%total%</strong> %contractCommon% selected";
$lang->contract->browse       = "{$lang->contract->common} List";
$lang->contract->appointedParty  = $lang->contract->ap;
$lang->contract->contractManager =$lang->contract->cm  ;
$lang->contract->createdDate    ="Created Date";
$lang->contract->openedBy       ="Created By";
$lang->contract->sign           ="Need Sign?";
$lang->contract->user           ="User";
$lang->contract->sequence           ="Approval Sequence";
$lang->contract->invoiceList ="Invoice List";
$lang->contract->createInvoice ="Create Invoice";
$lang->contract->deleteInvoice =$lang->invoice->deleteInvoice;
$lang->contract->editinvoice =$lang->invoice->editinvoice;
$lang->contract->approval   ="Approval List";
$lang->contract->approveDate="Respone Date";
$lang->contract->signature  ="Signature";
$lang->contract->detail     ="Contract Details";
$lang->contract->status     ='Status';




