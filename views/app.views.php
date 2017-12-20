<?php 

function at_dashboard_display_widget(){
global $wpdb;
$site_url = site_url();
$AT_BASE = plugins_url( '' , __FILE__ );

$current_user = wp_get_current_user();
$userPhone = $current_user->user_login;

$defaultDate = date("d/m/Y",strtotime("+1 day"));
$defaultHour = date("H:i");

/**
  *  THE DASHBOARD
  **/

   
ob_start();
?>
<div id="loading" ng-show="loading">
   <div class="main-loading-container" style="text-align:center" >Loading...</div>
</div>
<div ng-controller="userDataCtrl"> </div>
<div class="row">
   <div id="sidebar" class="col-md-2 visible-md visible-lg" style="">
      <?php at_display_user_menu(); ?>
   </div>
   <!-- #sidebar -->

   <div class="col-md-10 wrapper">
      <div id="content">
         <!-- sections -->
         <section ng-controller="historyCtrl" id="dashboard" class="content-page" ng-if="isDomains" ng-class="{'current': isDomains == 1}">
            <div class="row">
               <div class="col-md-12 content-header">
                  <h1 class="pull-left"><?php _e('Mes domaines', 'active-texto'); ?></h1>
                  <a href="#/register" class="btn btn-primary pull-right new">
                     <i class="icon-budicon-473_"></i><?php _e('Enrégistrer un domaine', 'manager'); ?>
                  </a>
               </div>
            </div>
	    <!-- .row -->
	    <div class="row">
               
	    </div>
	    <!-- .row -->
           <div class="row">
               <!-- History Box widget -->
               <div class="col-md-12 loaded">
	          <div class="widget-content" ng-show="!loading">
                     <table id="reports" class="table table-condensed table-striped table-bordered" style="">
                        <thead>
                           <tr>
                              <th width="50">#</th>
                              <th>Domain</th>
                              <th width="200"><?php _e("Actions", "active-texto"); ?></th>
                              <th width="120"><?php _e("Expiration", "active-texto"); ?></th>
                              <th style="text-align:right" width="127"><?php _e("Statut", "active-texto"); ?></th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr data-ng-repeat="domain in currentPageDomains" 
                               ng-click="showDetails(campaign.OutgoingGroupId)" 
                               title="click to see details"
                               style="cursor:pointer"
                             ><td>{{domains.length - $index - numPerPage*(currentPage-1)}}</td>
                              <td>{{domain.domain_name| cut:true:50:'...'}}</td>
                              <td style="text-align:right">
                                 <span showonparenthover_>
                                    <span class="actions" style="cursor:pointer" 
                                       ng-click="useAsModel(campaign.OutgoingGroupId)"
                                       title = "Utiliser ceci comme modèle pour un nouveau message"
                                       eatclick
                                   ><i class="icon-budicon-687"></i>
                                    </span>
                                 </span>
                              </td>
                              <td>{{domain.domain_expiry_date | cut:true:10:' '}}</td>
                              <td>{{domain.status}}</td>
                              </td>
                              
                           </tr>
                        </tbody>
                     </table>
                     <footer class="table-footer">
                        <div class="row">
                           <div class="col-md-6 page-num-info">
                              <span>
                                 Show 
                                 <select data-ng-model="numPerPage"
                                    data-ng-options="num for num in numPerPageOpt"
                                    data-ng-change="onNumPerPageChange()"
                                    class="ui-select">
                                 </select> 
                                 entries per page
                              </span>
                           </div>
                           <div class="col-md-6 text-right pagination-container">
                              <pagination class="pagination-sm"
                                ng-model="currentPage"
                                total-items="filteredDomains.length"
                                max-size="4"
                                ng-change="select(currentPage)"
                                items-per-page="numPerPage"
                                rotate="false"
                                previous-text="&lsaquo;" next-text="&rsaquo;"
                                boundary-links="true">
                              </pagination>
                           </div>
                        </div>
                     </footer>
                     <div id="historyModal">
                        <script type="text/ng-template" id="historyModalContent.html">
                           <div class="modal-header">
                               <button type="button" class="close" data-dismiss="modal" ng-click="close()">
                                  <span aria-hidden="true">&times;</span>
                                  <span class="sr-only">Close</span>
                               </button>
                               <h4 class="modal-title" id="mModalLabel"><?php _e("Détails", "active-texto"); ?></h4>
                           </div>
                           <div class="modal-body">
                              <span><?php _e("Message de: ", "active-texto"); ?><strong>{{selectedCampaign._from}}</strong></span>
                              <div class="alert alert-info">{{selectedCampaign.OutgoingMess}}</div>
                              <div class="pull-right">
                                 <a href="<?php echo $AT_BASE; ?>/../csv.php?c={{selectedCampaign.OutgoingGroupId}}" 
                                    class="btn btn-default btn-xs">Download CSV
                                 </a>
                              </div>
                              <table height="200" class="table table-condensed table-striped table-responsive" style="overflow:auto">
                                 <thead>
                                    <tr>
                                       <th width="40"><div class="th">
                                        #
                                       </div></th>
                                       <th><div class="th">
                                        <?php _e("Date", "active-texto"); ?>
                                       </div></th>
                                       <th><div class="th">
                                          <?php _e("Destinataires", "active-texto"); ?>
                                       </div></th>
                                       <th><div class="th" style="text-align: right">
                                          <?php _e("Statut", "active-texto"); ?>
                                       </div></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr data-ng-repeat="(key, destinator) in selectedCampaign._to track by $index">
                                       <td width="40">{{key+1}}</td>
                                       <td>{{selectedCampaign.OutgoingDate | date:'yyyy-MM-dd HH:mm:ss'}}</td>
                                       <td>{{destinator}}</td>
                                       <td align="right">
                                          <span class="label label-success" 
                                             ng-if="(getSendStatus(destinator, selectedCampaign)=='delivered')">
                                             Delivered
                                          </span>
                                          <span class="label label-danger" 
                                             ng-if="(getSendStatus(destinator, selectedCampaign)=='failed')">
                                             failed
                                          </span>
                                          <span class="label label-warning" 
                                             ng-if="(getSendStatus(destinator, selectedCampaign)=='pending')">
                                             pending
                                          </span>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                           <div class="modal-footer">
                              <button class="btn btn-default btn-xs" ng-click="close()">Close</button>
                           </div>
                        </script>
                     </div>                
                  </div>
               </div>
            </div>

         </section>
         
         <section class="content-page" ng-if="isRegister" ng-class="{'current': isRegister == 1}" ng-controller="registerDomainCtrl">
            <div class="row">
               <div class="col-md-12 content-header">
                  <h1 class="pull-left"><?php _e("Enrégistrement de domain", "active-texto"); ?></h1>
               </div>
            </div>
	    <!-- .row -->  
            <div class="row">
               <div class="col-md-12">
                  <form class="form-inline" id="domain_availability_verification_form" 
                    method="post" style="background: #eeffe7; padding: 10px 14px">
                    <div class="col-sm-10">
                      <input class="form-control input-lg"
                        style="width:100%; height:48px"
                        id="domain_availability_verification_form" 
                        name="domain_availability_verification_form" 
                        placeholder="<?php _e('Saisissez un ou plusieurs noms .tg, .com, .net, .org, séparés par un espace', 'active-texto'); ?>"
                        ng-model=avalaibilityCheck.domains
                      ></input>
  	            </div>
                    <button class="btn btn-primary btn-lg" 
                       type="submit"
                       ng-click="checkDomainIsAvalaible()"
                       on__click="window.scrollTo(0, document.body.scrollHeight || document.documentElement.scrollHeight);"
                    ><?php _e('Vérifier', 'active-texto'); ?></button>
                  </form>
               </div>
               <div class="col-md-12">
                  <table id="search-domains-table" class="table table-condensed table-striped" style="">
                        <tbody>
                           <tr data-ng-repeat="domain in domains"
                               title="click to see details"
                             ><td width="4" ng-style="domain.cctldtg==true && {'background-color':'#4fcb1f', 'border-color':'#4fcb1f'}"> </td>
                              <td><b>{{domain.name}}</b></td>
                              <td width="100">€15</td>
                              <td width="200" align="right">
                                <a href="javascript:void(0)" ng-click="addToCart(domain)" 
                                  style="font-size:22px" ng-if="domain.status=='available' && domain.inCart==false">
                                  <i class="icon-budicon-132"></i>
                                </a>

                                <span style="color:red; font-size:12px" ng-if="domain.status=='available' && domain.inCart==true">
                                  <a href="#/cart"  class="btn btn-default btn-xs">Checkout</a>
                                  <a href="javascript:void(0)" ng-click="deleteDomainFromCart(domain.name)" style="margin-left:8px; color:red">
                                    <i class="icon-budicon-501"></i></a>
                                </span>

                                <span style="color:red; font-size:12px" ng-if="domain.status=='not_available'">
                                  <?php _e("Non disponible", "active-texto"); ?>
                                </span>

                                <span style="font-size:12px" ng-if="domain.status=='checking'">
                                  <?php _e("Vérification ...", "active-texto"); ?>
                                </span>

                              </td>
                              <td width="8"> </td>
                           </tr>
                        </tbody>
                     </table>
                     <a href="#/cart" class="pull-right new" ng-if="cartData.length">
                     Afficher le panier / poursuivre la commande</a>
                </div>
	    </div>
         </section>

         <section class="content-page" ng-if="isCart" ng-class="{'current': isCart == 1}" ng-controller="showCartCtrl">
            <div class="row">
               <div class="col-md-12 content-header">
                  <h1 class="pull-left"><?php _e('Votre panier', 'active-texto'); ?></h1>
                  <a href="<?php echo site_url(); ?>/search" class="btn btn-primary pull-right new">
                     <i class="icon-budicon-473_"></i><?php _e('&nbsp;Ajouter un domaine&nbsp;', 'manager'); ?>
                  </a>
               </div>
            </div>
	    <!-- .row -->
            <div class="row">
               <div class="col-md-8">
                  <table id="cart-domains-table" class="table table-condensed table-striped" style="">
                       <thead>
                           <tr class="bg-primary_">
                              <th ><?php _e('Nom', 'manager'); ?></th>
                              <th style="text-align:right"><?php _e('Prix', 'manager'); ?></th>
                              <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr data-ng-repeat="domain in cartData track by $index"
                               title="click to see details"
                             ><td><b>{{domain.name}}</b></td>
                              <td align="right">€15</td>
                              <td align="right" width="100" >
                                <span showonparenthover="" class="hide">
                                  <span class="actions" style="cursor:pointer" ng-click="deleteDomainFromCart(domain.name)" title="Supprimer" eatclick="">
                                    <i class="icon-budicon-501" style="color:#c31313"></i>
                                  </span>
                                </span>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <p ng-if="cartData.length == 0" class="alert alert-info"> <?php _e("Votre panier est vide", "active-texto"); ?></p>
                </div>
                <div class="col-md-4">
                   <table id="scheduled-table" class="table table-condensed table-bordered" style="">
                      <thead>
                           <tr class="bg-primary_">
                              <th ><?php _e("Votre commande", "active-texto"); ?> ({{cartData.length}} <?php _e("éléments", "active-texto"); ?>)</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td align="right" style="padding: 18px 8px"><strong>Total: €{{cartData.length * 15}}</strong></td>
                           </tr>
                           <tr ng-if="cartData.length">
                             <td align="center"><a href="#/payment" class="btn btn-success"><?php _e("Payer maintenant", "active-texto"); ?></a></td>
                           </tr>
                        </tbody>
                     </table>
                </div>
	    </div>
         </section>


	 <section class="content-page" ng-if="isPayment" ng-class="{'current': isPayment == 1}"  ng-controller="paymentCtrl">
	    <div class="row">
	       <div class="col-md-12 content-header">
		  <h1 class="pull-left"><?php _e('Paiement', 'active-texto'); ?></h1>
		  <a class="hide" href="<?php echo site_url(); ?>/search" class="btn btn-primary pull-right new" style="width:306px">
		     <i class="icon-budicon-473_"></i><?php _e('&nbsp;Ajouter un domaine&nbsp;', 'manager'); ?>
		  </a>
	       </div>
	    </div>
	    <!-- .row -->
	    <div class="row">
	       <div class="col-md-7">
               
               <span><?php _e('Comment souhaitez-vous payer?', 'manager'); ?></span>
		  
                   <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
             <ul class="nav nav-tabs" id="pmTabs" role="tablist">
                <li role="presentation" class="active">
                   <a href="javascript:void(0)" id="flooz-tab" role="tab" data-toggle="tab" aria-controls="flooz" aria-expanded="true" data-target="#flooz">Flooz</a>
                </li>
                <li role="presentation" class="">
                   <a href="javascript:void(0)" role="tab" id="tmoney-tab" data-toggle="tab" aria-controls="tmoney" aria-expanded="false" data-target="#tmoney">TMoney</a>
                </li>
             </ul>
             <div class="tab-content" id="pmTabContent">
                <div class="tab-pane fade active in" role="tabpanel" id="flooz">
                   <p> Flooz </p>
                </div>
                <div class="tab-pane fade" role="tabpanel" id="tmoney">
          
                <form name="loginform" method="post" class="form-vertical" role="form" action="" ng-show="requirePhone">
                 <div class="form-group">
                    <label for="phone" class="col-sm-5 control-label"><span class="small">Entrez votre numéro TMoney&nbsp;</span></label>
                    <div class="col-sm-7">
                        <input type="text" name="log" id="user_phone" value="" size="20" class="form-control" >
                    </div>
                 </div>

                 <div class="form-group">
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-success btn-mini" value="Payer maintenant" ng-click="saveCommand('9090')">
                     </div>
                 </div>
               </form>
                 
                <div ng-bind-html="QRCode"></div>
                </div>
             </div>
           </div>


		</div>
		<div class="col-md-5">
		   <table id="scheduled-table" class="table table-condensed table-bordered" style="">
		      <thead>
		           <tr class="bg-primary_">
		              <th colspan="2"><?php _e("Votre commande", "active-texto"); ?> ({{cartData.length}} <?php _e("éléments", "active-texto"); ?>)</th>
		           </tr>
		        </thead>
		        <tbody>
		           <tr data-ng-repeat="domain in cartData track by $index"
		               title="click to see details"
		             ><td><b>{{domain.name}}</b></td>
		              <td align="right">€15</td>
		           </tr>
		           <tr>
		              <td colspan="2" align="right" style="padding: 18px 8px"><strong>Total: €{{cartData.length * 15}}</strong></td>
		           </tr>
		           <tr>
		              <td colspan="2" align="center"> </td>
		           </tr>
		        </tbody>
		     </table>
		</div>
	    </div>
	 </section>

         <section ng-controller="scheduledCtrl" class="content-page" ng-if="isScheduled"  ng-class="{'current': isScheduled == true}" >
            <div class="row">
               <div class="col-md-12 content-header">
                  <h1 class="pull-left"><?php _e("Envois différés", "active-texto"); ?></h1>
                  <a href="#/compose" class="btn btn-primary pull-right new">
                     <i class="icon-budicon-473_"></i><?php _e("Envoyer des SMS", "active-texto"); ?></a>
               </div>
            </div>
	    <!-- .row -->
	    
            <div class="row">
               <!-- History Box widget -->
               <div class="col-md-12 loaded">
	          <div class="widget-content" ng-show="!loading">
                     <table id="scheduled-table" class="table table-condensed table-striped" style="">
                        <thead>
                           <tr>
                              <th width="70">#</th>
                              <th><?php _e("Date","active-texto"); ?></th>
                              <th><?php _e("Message","active-texto"); ?></th>
                              <th style="text-align:right"><?php _e("Destinataires","active-texto"); ?></th>
                              <th style="text-align:right" width="100"><?php _e("Statut","active-texto"); ?></th>
                              <th style="text-align:right" width="50"> </th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr data-ng-repeat="campaign in currentPageCampaigns" 
                               ng-click="showDetails(campaign.OutgoingGroupId)" 
                               title="click to see details"
                               style="cursor:pointer"
                             ><td>{{campaigns.length - $index - numPerPage*(currentPage-1)}}</td>
                              <td>{{campaign.OutgoingDate | date:'yyyy-MM-dd HH:mm:ss'}}</td>
                              <td>{{campaign.OutgoingMess| cut:true:50:'...'}}</td>
                              <td style="text-align:right">{{campaign._to.length}}</td>
                              <td style="text-align:right">
                                 <span class="label label-default"><?php _e('Envoi différé','active-texto'); ?>
                                 </span>
                              </td>
                              <td style="text-align:right">
                                 <span showonparenthover>
                                    <span class="actions" style="cursor:pointer" 
                                       ng-click="useAsModel(campaign.OutgoingGroupId)"
                                       title = "<?php _e('Utiliser ceci comme modèle pour un nouveau message','active-texto'); ?>"
                                       eatclick
                                   ><i class="icon-budicon-687"></i>
                                    </span>
                                    <span title="Supprimer" style="cursor:pointer; height:6px" 
                                          ng-click="deleteScheduled([campaign.OutgoingGroupId])" 
                                          eatclick>
                                       <i class="icon-budicon-501" style="color:#c31313"></i>
                                    </span>
                                 </span>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <footer class="table-footer">
                        <div class="row">
                           <div class="col-md-6 page-num-info">
                              <span>
                                 <?php _e('Afficher','active-texto'); ?>
                                 <select data-ng-model="numPerPage"
                                    data-ng-options="num for num in numPerPageOpt"
                                    data-ng-change="onNumPerPageChange()"
                                    class="ui-select">
                                 </select> 
                                 <?php _e('éléments par page','active-texto'); ?>
                              </span>
                           </div>
                           <div class="col-md-6 text-right pagination-container">
                              <pagination class="pagination-sm"
                                ng-model="currentPage"
                                total-items="filteredCampaigns.length"
                                max-size="4"
                                ng-change="select(currentPage)"
                                items-per-page="numPerPage"
                                rotate="false"
                                previous-text="&lsaquo;" next-text="&rsaquo;"
                                boundary-links="true">
                              </pagination>
                           </div>
                        </div>
                     </footer>
                     <div id="historyModal">
                        <script type="text/ng-template" id="scheduledModalContent.html">
                           <div class="modal-header">
                               <button type="button" class="close" data-dismiss="modal" ng-click="close()">
                                  <span aria-hidden="true">&times;</span>
                                  <span class="sr-only"><?php _e('Fermer','active-texto'); ?></span>
                               </button>
                               <h4 class="modal-title" id="mModalLabel">Scheduled details</h4>
                           </div>
                           <div class="modal-body">
                              <span>Message de: <strong>{{selectedCampaign._from}}</strong> - <?php _e('Envoi programé pour le: ','active-texto'); ?> {{selectedCampaign.SendOn | date:'yyyy-MM-dd HH:mm'}} </span>
                              <div class="alert alert-info">{{selectedCampaign.OutgoingMess}}</div>
                              <div class="pull-right">
                                 <a href="<?php echo $AT_BASE; ?>/../csv.php?c={{selectedCampaign.OutgoingGroupId}}" 
                                    class="btn btn-default btn-xs"><?php _e('Télécharger en CSV','active-texto'); ?>
                                 </a>
                              </div>
                              <table height="200" class="table table-condensed table-striped table-responsive" style="overflow:auto">
                                 <thead>
                                    <tr>
                                       <th width="40"><div class="th">
                                        #
                                       </div></th>
                                       <th><div class="th">
                                         <?php _e('Date','active-texto'); ?>
                                       </div></th>
                                       <th><div class="th">
                                          <?php _e('Destinataires','active-texto'); ?>
                                       </div></th>
                                       <th><div class="th" style="text-align: right">
                                          <?php _e('Statut','active-texto'); ?>
                                       </div></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr data-ng-repeat="(key, destinator) in selectedCampaign._to track by $index">
                                       <td width="40">{{key+1}}</td>
                                       <td>{{selectedCampaign.OutgoingDate | date:'yyyy-MM-dd HH:mm:ss'}}</td>
                                       <td>{{destinator}}</td>
                                       <td align="right">
                                          <span class="label label-default">
                                             <?php _e('Envoi différé','active-texto'); ?>
                                          </span>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                           <div class="modal-footer">
                              <button class="btn btn-default btn-xs" ng-click="close()"><?php _e('Fermer','active-texto'); ?></button>
                           </div>
                        </script>
                     </div>                
                  </div>
               </div>
            </div>
         </section>
                
         <section id="api" class="content-page" ng-if="isAPI" ng-class="{'current': isAPI == 1}">
            <div class="row">
               <div class="col-md-12 content-header">
                  <h1 class="pull-left"><?php _e("API", "active-texto"); ?></h1>
               </div>
            </div>
	    <!-- .row -->
	    <div class="row">
            <div class="col-md-12">
                <?php _e("Nous travaillons dur pour rendre disponible ce service très prochainement.", "active-texto"); ?>
            </div>
	    </div>
	    <!-- .row -->
         </section>
             
             
         <section id="running-tasks" class="content-page" style="display:block" ng-controller="currentRunningTasksCtrl">
            <div ng-if="runningTasks.length > 0">
               <div class="row">
                  <div class="col-md-12 content-header" style="border-bottom:1px solid #efefef">
                     <h4 class="pull-left"><?php _e('Tâches en cours d\'exécution', 'active-texto'); ?></h4>
                  </div>
               </div>
	       <!-- .row -->
	              
               <div class="row">
                  <!-- Stat Boxes widget -->
                  <div class="client-stat-boxes col-md-12 loaded">
	             <div class="widget-content row row-stats">
                        <div class="col-md-3 stat-box-users" ng-repeat="task in runningTasks" >
                           <small><?php _e('De','active-texto'); ?> {{task._from}} <?php _e('à','active-texto'); ?> {{task._to.length}} contacts</small>
                           <br/>
                           <span>
                               {{task.position}} <?php _e('sur','active-texto'); ?> 
                               {{task._to.length}} <?php _e('envoyés','active-texto'); ?></span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </section>
         
         <!-- end sections -->
      </div>
   </div>
</div>
<div ng-view></div>
<script type="text/javascript">

   jQuery('document').click(function (e) {
     //e.preventDefault()
     //jQuery(this).tab('show')
   })

   $(function () {
       //$('table tr td span.action').hover(function(){ alert($(this).children().size()); });
   });
</script>
<br/>
<div class="row">
<div class="col-xs-12">
   <?php  
    /*for($i=0; $i<850; $i++){
    echo '<span style="font-size:26px"><i class="icon-budicon-'.$i.'"></i></span> ';
   }*/
   ?>
</div>
</div>

<?php
	$overview = ob_get_contents();
	ob_end_clean();
	
	return $overview;
}
