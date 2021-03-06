<?php
/**
 * The view view of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: view.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class="main-row">
  <div class="col-8 main-col">
    <div class="row">
    <?php //$isRoadmap = common::hasPriv('product', 'roadmap');?>
    <?php if($isRoadmap):?>
      <div class="col-sm-6">
        <div class="panel block-release">
          <div class="panel-heading">
            <div class="panel-title"><?php echo $lang->product->roadmap;?></div>
          </div>
          <div class="panel-body">
            <div class="release-path"> 
              <ul class="release-line">
                <?php foreach($roadmaps as $roadmap):?>
                <?php if(isset($roadmap->begin)):?>
                <li <?php if(date('Y-m-d') < $roadmap->begin) echo "class='active'";?>>
                  <a href="<?php echo $this->createLink('productplan', 'view', "planID={$roadmap->id}");?>">
                    <span class="title" title='<?php echo $roadmap->title;?>'><?php echo $roadmap->title;?></span>
                    <span class="date"><?php echo $roadmap->begin;?></span>
                  </a>
                </li>
                <?php else:?>
                <li>
                  <a href="<?php echo $this->createLink('release', 'view', "releaseID={$roadmap->id}");?>">
                    <span class="title" title='<?php echo $roadmap->name;?>'><?php echo $roadmap->name;?></span>
                    <span class="date"><?php echo $roadmap->date;?></span>
                  </a>
                </li>
                <?php endif;?> 
                <?php endforeach;?>
              </ul>
            </div>
            <?php echo html::a($this->createLink('product', 'roadmap', "productID={$product->id}"), $lang->product->iterationView . "<span class='label label-badge label-icon'><i class='icon icon-arrow-right'></i></span>", '', "class='btn btn-primary btn-circle btn-icon-right btn-sm pull-right'");?>
          </div>
        </div>
      </div>
      <?php endif;?>
      <div class="col-sm-<?php echo 12?>">
        <div class="panel block-dynamic">
          <div class="panel-heading">
          <div class="panel-title"><?php echo $lang->invoice->detail;?></div>
            <nav class="panel-actions nav nav-default">
              <!--<li><a href="<?php// echo $this->createLink('product', 'dynamic', "productID={$product->id}&type=all");?>" title="<?php //echo $lang->more;?>"><i class="icon icon-more icon-sm"></i></i></a></li>-->
            </nav>
          </div>
          <div class="panel-body scrollbar-hover">
              <?php //foreach($dynamics as $action):?>
              <!--<li <?php //if($action->major) echo "class='active'";?>>
                <div class='text-ellipsis'>
                  <span class="timeline-tag"><?php //echo $action->date;?></span>
                  <span class="timeline-text"><?php //echo zget($users, $action->actor) . ' ' . "<span class='label-action'>{$action->actionLabel}</span>" . $action->objectLabel . ' ' . html::a($action->objectLink, $action->objectName);?></span>
                </div>
              </li>-->
              <?php //endforeach;?>
            <div class="detail">
            <div class="detail-title "><strong><?php echo $lang->invoice->basicInfo;?></strong></div>
            <div class="detail-content">
              <table class="table table-data data-basic">
                <tbody> 
                <tr>
                    <th class="w-120px"><?php echo $lang->invoice->invoiceID;?></th>
                    <td><em><?php echo $invoice->id;?></em></td>
                    <th ><?php echo $lang->invoice->status;?></th>
                    <td><em><b><?php echo $invoice->status;?></b></em></td>
                  </tr>
                  <tr>
                    <th><?php echo $lang->invoice->submitteddate?></th>
                    <td><em><?php echo $invoice->submitdate;?></em></td>
                  </tr>
                  <tr>
                    <th><?php echo $lang->invoice->amount;?></th>
                    <td><em><?php echo '$'.$invoice->amount;?></em></td>
                  </tr>
                  <tr>
                    <th><?php echo $lang->invoice->step;?></th>
                    <td><em><?php echo $invoice->step;?></em></td>
                  </tr>
                  <?php $invoiceStat = array_combine($invoice->item, $invoice->price);?>
                  <?php foreach($invoiceStat as $key => $value):?>
                  <tr>
                    <th><?php echo $lang->invoice->item;?></th> <td> <em><?php echo $key;?></em> </td>
                    <th><?php echo $lang->invoice->price;?></th> <td> <em><?php echo '$'.$value;?></em> </td>
                  </tr>
                  <?php endforeach;?>
                  <tr>
                    <th><?php echo $lang->contract->desc;?></th>
                    <td><em><?php echo $invoice->description;?></em></td>
                  </tr>
                  <tr>
                    <th><?php echo $lang->invoice->softcopy;?></th>
                    <td><em>
                      
                    <?php
                    foreach($softcopy as $file){
                      echo "<a href='".helper::createlink('file','download',"fileID=$file->id")."'>$file->title</a>";
                      break;
                    }?>
                    </em></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          </div>
        </div>
      </div>
      <?php $this->printExtendFields($product, 'div', "position=left&inForm=0");?>
      <!--<div class="col-sm-12">
        <?php $blockHistory = true;?>
        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=product&objectID=$product->id");?>
        <?php include '../../common/view/action.html.php';?>
        <?php //echo $this->fetch('file', 'printFiles', array('files' => $invoice->file, 'fieldset' => 'false'));?>
      </div>
       -->
    </div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php
        $params = "product=$product->id";
        $browseLink = inlink('invoicelist', "contractID=$invoice->contractID");
        common::printBack($browseLink);
        if($invoice->status=='pending'){
          if($contract->appointedParty==$this->app->user->account||$contract->appointedParty=='admin'){
            common::printIcon('contract', 'Submit', "invoiceID=".$invoice->id, $invoice, 'button', '', '', 'iframe', true);
            echo "<div class='divider'></div>";  
            common::printIcon('contract', 'editinvoice', "invoiceID=".$invoice->id, "test");
            echo "<div class='divider'></div>";  
            common::printIcon('contract', 'deleteInvoice', $params, $product, 'button', 'trash', 'hiddenwin');
          }
        }else if($invoice->status=='submitted') {
          if(in_array($this->app->user->account,$approver)){// can user approve?
            $u=$this->dao->select('*')->from('zt_approval')->where('objectID')->eq($invoice->id)->andWhere('objectType')->eq('invoice')->andWhere("User")->eq($this->app->user->account)->fetch();
            $u->sign==0?common::printIcon('contract', 'Approve', "invoiceID=".$invoice->id, $invoice, 'button', '', '', 'iframe', true):common::printIcon('contract', 'ApproveWithSign', "invoiceID=".$invoice->id, $invoice, 'button', '', '', 'iframe', true);
            common::printIcon('contract', 'Reject', "invoiceID=".$invoice->id, $invoice, 'button', '', '', 'iframe', true);
          }
        }else if($invoice->status=='rejected') {// should only deleted by appointedParty Or CM? 
            common::printIcon('contract', 'deleteInvoice', "invoiceID=".$invoice->id, $invoice, 'button', 'trash', 'hiddenwin');
        }else if($invoice->status=='approved') {
            common::printIcon('contract', 'payment', "invoiceID=".$invoice->id, $invoice, 'button', '', '', 'iframe', true);
        }else if($invoice->status=='paid'){
            common::printIcon('contract', 'exportpdf', "invoiceID=".$invoice->id, $invoice,'button', '', '', 'iframe', true);
        }  

        ?>
        
      </div>
    </div>
  </div>
  <div class="col-4 side-col">
    <div class="cell">
      <div class='tabs'>
        <ul class='nav nav-tabs'>
          <li class='active'><a href='#legendBasic' data-toggle='tab'><?php echo $lang->invoice->approvallist;?></a></li>
          <li><a href='#legendLife' data-toggle='tab'><?php echo $lang->contract->detail;?></a></li>
          <?php if(!empty($task->team)) :?>
          <li><a href='#legendTeam' data-toggle='tab'><?php echo $lang->task->team;?></a></li>
          <?php endif;?>
        </ul>
        <div class='tab-content'> 
          <div class='tab-pane active' id='legendBasic'>
            <table class="pure-table pure-table-horizontal" style="width:100%">
              <thead>
                <tr>
                    <th><?php echo $lang->contract->sequence;?></th>
                    <!--<th><?php //echo $lang->approval->position;?></th>-->
                    <th><?php echo $lang->contract->user;?></th>
                    <th><?php echo $lang->contract->signature;?></th>
                    <th><?php echo $lang->contract->status;?></th>
                    <th><?php echo $lang->contract->approveDate;?></th>
                    <th><?php echo $lang->contract->desc;?></th>

                </tr>
              </thead>
        
              <tbody>
                <?php foreach($approvalStats as $approval):?>
                    <tr <?php if($approval->approveDate==NULL && $approval->order==$invoice->step ){ echo "style='background-color:yellow;'"; }?>>
                      <td> <?php echo $approval->order;?></td>
                      <td> <?php echo $approval->user;?></td>

                      <td> 
                      <?php 
			if($approval->status=="approved" && $approval->sign=="true"):?>
                        <?php echo "Image".$approval->signature;?> 
                      <?php else: ?>
                        <?php echo "N/A";?>
                        <?php endif;?>
                      </td>
                      <td> <?php echo $approval->status;?></td>
                      <td> <?php echo $approval->approveDate;?></td>

                      <td>
                        <?php if($approval->status!="waiting"):?>
                         <a href='<?php echo helper::createlink('contract','viewApproval',"apID=$approval->id");?>'>View</a></td>
                          <?php endif; ?>
                    </tr>
                <?php endforeach;?>
                
              </tbody>
            </table>
          </div>
          <div class='tab-pane' id='legendLife'>
            <table class='table table-data' style="width:100%"> <tr>
            <tr>
                <th class="w-150px"><?php echo $lang->contract->contractID;?></th>
                <td><?php echo $invoice->contractID;?></td>
              </tr>
              <th><?php echo $lang->contract->name;?></th>
                    <td><em><?php echo html::a($this->createLink('contract', 'view', "contract=$invoice->contractID" ), $contract->contractName);?> <!-- changed the hyperlink to invoiceview 2020.1.10--></em></td>
              </tr>

              <tr>
                    <th><?php echo $lang->contract->eoRef?></th>
                    <td><em><?php echo $invoice->refNo;?></em></td>
              </tr> 
           
              <tr>
                <th><?php echo $lang->contract->amountInvoiceview;?></th>
                <td><?php echo $invoice->totalAmount;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->product->name;?></th>
                <td><?php echo $asset->name;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->contract->cm;?></th>
                <td><?php 
			$cm=json_decode($contract->contractManager,true);
			if(is_array($cm)){

			foreach($cm as $ppl){
			//	zget()
				echo $ppl."</br>";
			}
			}else{	echo $contract->contractManager;}


		?>
		</td>
              </tr>
              <tr>
                <th><?php echo $lang->contract->ap;?></th>
                <td><?php echo $contract->appointedParty;?></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="cell">
          <div class="detail">
            <!--<h2 class="detail-title"><span class="label-id"><?php //$lang->invoice->AccountStatus;?></span> <span class="label label-light label-outline"><?php //echo $product->code;?></span> <?php //echo $product->name;?></h2>
            <div class="detail-title"><strong><?php //echo $lang->invoice->AccountStatus.':'.' '.$invoice->status;?></strong></div>
            <div class="detail-content article-content">
            <?php //foreach($approvalStats as $approval):?>
                  <div>
                    <th><?php //echo $lang->approval->user;?></th> <td> <em><?php //echo ': '.$approval->user;?></em></td>
                    <th><?php //echo $lang->approval->sign;?></th> <td> <em><?php //echo ': '.$approval->sign;?></em> </td> 
                    <th><?php //echo $lang->approval->approveDate;?></th> <td> <em><?php //echo ': '.$approval->approveDate;?></em> </td>
                  </div>
            <?php //endforeach;?>-->
              <!--<p><span class="text-limit" data-limit-size="40"><?php //echo $product->desc;?></span><a class="text-primary text-limit-toggle small" data-text-expand="<?php //echo $lang->expand;?>"  data-text-collapse="<?php //echo $lang->collapse;?>"></a></p>-->
              <!--<p>
                <span class="label label-primary label-outline"><?php //echo $lang->product->typeAB . ':' . zget($lang->product->typeList, $product->type);?></span>
                <span class="label label-success label-outline"><?php //echo $lang->product->status . ':' . $this->processStatus('product', $product);?></span>
                <?php //if($product->deleted):?>
                <span class='label label-danger label-outline'><?php //echo $lang->product->deleted;?></span>
                <?php //endif; ?>
              </p>-->
            </div>
          </div><!--
          <?php //if($product->type == 'platform'):?>
          <div class="detail">
          <div class="detail-title"><strong><?php //echo $lang->product->branchName['platform'];?></strong><a class="btn btn-link pull-right muted"><i class="icon icon-more icon-sm"></i></a></div>
            <div class="detail-content">
              <ul class="clearfix branch-list">
                <?php //foreach($branches as $branchName):?>
                <li><?php //echo $branchName;?></li>
                <?php //endforeach;?>
                <li><a class="text-muted" href="<?php //echo $this->createLink('branch', 'manage', "productID={$product->id}")?>"><i class="icon icon-plus hl-primary text-primary"></i> &nbsp;<?php //echo $lang->branch->add;?></a></li>
              </ul>
            </div>
          </div>
          <?php //endif;?>
          <div class="detail">
              <div class="detail-title"><strong><?php //echo $lang->product->manager;?></strong></div>
            <div class="detail-content">
              <table class="table table-data">
                <tbody>
                  <tr>
                    <th class='w-100px'><i class="icon icon-person icon-sm"></i> <?php //echo $lang->productCommon;?></th>
                    <td><em><?php //echo zget($users, $product->PO);?></em></td>
                    <th><i class="icon icon-person icon-sm"></i> <?php //echo $lang->product->qa;?></th>
                    <td><em><?php //echo zget($users, $product->QD);?></em></td>
                  </tr>
                  <tr>
                    <th><i class="icon icon-person icon-sm"></i> <?php //echo $lang->product->release;?></th>
                    <td><em><?php //echo zget($users, $product->RD);?></em></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="detail">
            <div class="detail-title"><strong><?php// echo $lang->product->basicInfo;?></strong></div>
            <div class="detail-content">
              <table class="table table-data data-basic">
                <tbody>
                  <tr>
                    <th><?php// echo $lang->product->line;?></th>
                    <td><em><?php //echo zget($lines, $product->line);?></em></td>
                  </tr>
                  <tr>
                    <th><?php// echo $lang->story->openedBy?></th>
                    <td><em><?php //echo zget($users, $product->createdBy);?></em></td>
                  </tr>
                  <tr>
                    <th><?php //echo $lang->story->openedDate?></th>
                    <td><em><?php //echo formatTime($product->createdDate, DT_DATE1);?></em></td>
                  </tr>
                  <tr>
                    <th><?php //echo $lang->product->acl;?></th>
                    <td><em><?php //echo $lang->product->aclList[$product->acl];?></em></td>
                  </tr>
                  <?php //if($product->acl == 'custom'):?>
                  <tr>
                    <th><?php //echo $lang->product->whitelist;?></th>
                    <td>
                      <em>
                        <?php
                        //$whitelist = explode(',', $product->whitelist);
                        //foreach($whitelist as $groupID) if(isset($groups[$groupID])) echo $groups[$groupID] . '&nbsp;';
                        ?>
                      </em>
                    </td>
                  </tr>
                  <?php //endif;?>
                </tbody>
              </table>
            </div>
          </div>
          <?php //if($config->global->flow != 'onlyTest'):?>
          <div class="detail">
            <div class="detail-title"><strong><?php //echo $lang->product->otherInfo;?></strong></div>
            <div class="detail-content">
              <table class="table table-data data-basic">
                <tbody>
                  <?php //$space = common::checkNotCN() ? ' ' : '';?>
                  <tr>
                    <th><?php //echo $lang->story->statusList['active']  . $space . $lang->story->common;?></th>
                    <td><em><?php //echo $product->stories['active']?></em></td>
                    <th><?php //echo $lang->product->plans?></th>
                    <td><em><?php //echo $product->plans?></em></td>
                    <th class='w-80px'><?php //echo $lang->product->bugs?></th>
                    <td><em><?php //echo $product->bugs?></em></td>
                  </tr>
                  <tr>
                    <th><?php //echo $lang->story->statusList['changed']  . $space . $lang->story->common;?></th>
                    <td><em><?php //echo $product->stories['changed']?></em></td>
                    <th><?php //echo $lang->product->projects?></th>
                    <td><em><?php //echo $product->projects?></em></td>
                    <th><?php //echo $lang->product->cases?></th>
                    <td><em><?php //echo $product->cases?></em></td>
                  </tr>
                  <tr>
                    <th><?php //cho $lang->story->statusList['draft']  . $space . $lang->story->common;?></th>
                    <td><em><?php //echo $product->stories['draft']?></em></td>
                    <th><?php //echo $lang->product->builds?></th>
                    <td><em><?php //echo $product->builds?></em></td>
                    <th><?php //echo $lang->product->docs?></th>
                    <td><em><?php //echo $product->docs?></em></td>
                  </tr>
                  <tr>
                    <th><?php //echo $lang->story->statusList['closed']  . $space . $lang->story->common;?></th>
                    <td><em><?php //echo $product->stories['closed']?></em></td>
                    <th><?php //echo $lang->product->releases?></th>
                    <td><em><?php //echo $product->releases?></em></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>-->
          <?php //endif;?>
          <?php //$this->printExtendFields($product, 'div', "position=right&inForm=0&inCell=1");?>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="mainActions" class='main-actions'>
  <nav class="container"></nav>
</div>
<?php include '../../common/view/footer.html.php';?>
