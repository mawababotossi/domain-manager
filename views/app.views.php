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
                  <h1 class="pull-left"><?php _e('Mes domaines', 'domain-manager'); ?></h1>
                  <a href="<?php echo site_url(); ?>/search" class="btn btn-primary pull-right new">
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
                     <table id="reports" class="table table-condensed table-striped" style="">
                        <thead>
                           <tr>
                              <th width="50">#</th>
                              <th>Domain</th>
                              <th width="200"><?php _e("Actions", "domain-manager"); ?></th>
                              <th><?php _e("Enrégistrement", "domain-manager"); ?></th>
                              <th><?php _e("Expiration", "domain-manager"); ?></th>
                              <th style="text-align:left" width="127"><?php _e("Statut", "domain-manager"); ?></th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr data-ng-repeat="domain in currentPageDomains"
                             ><td>{{domains.length - $index - numPerPage*(currentPage-1)}}</td>
                              <td>{{domain.domain_name| cut:true:50:'...'}}</td>
                              <td style="text-align:left">
                                 <span showonparenthover_ ng-if="domain.status=='Active'">
                                    <a href="javascript:void(0)" class="actions" style="cursor:pointer" 
                                       ng-click="whoisDomain(domain.domain_name)"
                                       title = "Whois"
                                       eatclick
                                   ><i class="icon-budicon-377"></i>
                                    </a> &nbsp;
                                   
                                    <a href="#/domains/{{domain.domain_name}}" class="actions" style="cursor:pointer" 
                                       title = "Modifier ce domaine"
                                   ><i class="icon-budicon-331"></i>
                                    </a> &nbsp;

                                    <a href="javascript:void(0)" class="actions" style="cursor:pointer" 
                                       ng-click="renewDomain(domain.domain_name)"
                                       title = "Renouvelé ce domaine"
                                       eatclick
                                   ><i class="icon-budicon-435"></i>
                                    </a>
                                 </span>
                              </td><td>{{domain.domain_creation_date | cut:true:10:' '}}</td>
                              <td>{{domain.domain_expiry_date | cut:true:10:' '}}</td>
                              <td>
                                 <span class="label" ng-class="{Active:'label-success', Pending:'label-warning', Expiring:'label-danger'}[domain.status]"> {{domain.status}} </span>
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
                               <h4 class="modal-title" id="mModalLabel"><?php _e("Whois", "domain-manager"); ?></h4>
                           </div>
                           <div class="modal-body">
                              <pre>{{whoisContent|sanitize}}</pre>
                             
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


        <section ng-controller="dnsInfoCtrl" class="content-page" ng-if="isDnsInfo"  ng-class="{'current': isDnsInfo == true}" >
            <div class="row">
               <div class="col-md-12 content-header">
                  <h1 class="pull-left">{{domain_name}}</h1>
                  
               </div>
            </div>
	    <!-- .row -->
	    
            <div class="row">
               <!-- History Box widget -->
               <div class="col-md-12 loaded">
	          <div class="widget-content" ng-show="!loading">
                     <table id="domains-dns-table" class="table table-condensed table-striped" style="">
                       <thead>
                           <tr>
                              <th width="70"><?php _e('Type', 'manager'); ?></th>
                              <th><?php _e('Hostname', 'manager'); ?></th>
                              <th><?php _e('Value', 'manager'); ?></th>
                              <th width="120" align="right"><?php _e('TTL', 'manager'); ?></th>
                              <th width="100"></th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr data-ng-repeat="v in domain_records"
                               title="click to see details"
                             ><td>{{v.type}}</td>
                              <td>{{v.hostname}}</td>
                              <td>{{v.data}}</td>
                              <td align="right">{{v.ttl}}</td>
                              <td align="right">
                                 <span showonparenthover >
                                    <a href="javascript:void(0)" class="actions" style="cursor:pointer" 
                                       ng-click="editDomainRecord(v.id)"
                                       title = "Edit record"
                                       eatclick
                                   ><i class="icon-budicon-274"></i>
                                    </a> &nbsp;
                                  
                                    <a href="javascript:void(0)" class="actions" style="cursor:pointer; color:red" 
                                       ng-click="deleteDomainRecord(v.id)"
                                       title = "Supprimer ce enregistrement"
                                       eatclick
                                   ><i class="icon-budicon-501"></i>
                                    </a>
                                 </span>
                              </td>
                           </tr>
                        </tbody>
                     </table>         
                  </div>
               </div>
            </div>
         </section>
         
         <section class="content-page" ng-if="isRegister" ng-class="{'current': isRegister == 1}" ng-controller="registerDomainCtrl">
            <div class="row">
               <div class="col-md-12 content-header">
                  <h1 class="pull-left"><?php _e("Enrégistrement de domain", "domain-manager"); ?></h1>
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
                        placeholder="<?php _e('Saisissez un ou plusieurs noms .tg, .com, .net, .org, séparés par un espace', 'domain-manager'); ?>"
                        ng-model=avalaibilityCheck.domains
                      ></input>
  	            </div>
                    <button class="btn btn-primary btn-lg" 
                       type="submit"
                       ng-click="checkDomainIsAvalaible()"
                       on__click="window.scrollTo(0, document.body.scrollHeight || document.documentElement.scrollHeight);"
                    ><?php _e('Vérifier', 'domain-manager'); ?></button>
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
                                  <?php _e("Non disponible", "domain-manager"); ?>
                                </span>

                                <span style="font-size:12px" ng-if="domain.status=='checking'">
                                  <?php _e("Vérification ...", "domain-manager"); ?>
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
                  <h1 class="pull-left"><?php _e('Votre panier', 'domain-manager'); ?></h1>
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
                     <p ng-if="cartData.length == 0" class="alert alert-info"> <?php _e("Votre panier est vide", "domain-manager"); ?></p>
                </div>
                <div class="col-md-4">
                   <table id="scheduled-table" class="table table-condensed table-bordered" style="">
                      <thead>
                           <tr class="bg-primary_">
                              <th ><?php _e("Votre commande", "domain-manager"); ?> ({{cartData.length}} <?php _e("éléments", "domain-manager"); ?>)</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td align="right" style="padding: 18px 8px"><strong>Total: €{{cartData.length * 15}}</strong></td>
                           </tr>
                           <tr ng-if="cartData.length">
                             <td align="center"><a href="#/payment" class="btn btn-success"><?php _e("Payer maintenant", "domain-manager"); ?></a></td>
                           </tr>
                        </tbody>
                     </table>
                </div>
	    </div>
         </section>


	
         
                
         <section id="api" class="content-page" ng-if="isAPI" ng-class="{'current': isAPI == 1}">
            <div class="row">
               <div class="col-md-12 content-header">
                  <h1 class="pull-left"><?php _e("API", "domain-manager"); ?></h1>
               </div>
            </div>
	    <!-- .row -->
	    <div class="row">
            <div class="col-md-12">
                <?php _e("Nous travaillons dur pour rendre disponible ce service très prochainement.", "domain-manager"); ?>
            </div>
	    </div>
	    <!-- .row -->
         </section>
             
             
         <section id="running-tasks" class="content-page" style="display:block" ng-controller="currentRunningTasksCtrl">
            <div ng-if="runningTasks.length > 0">
               <div class="row">
                  <div class="col-md-12 content-header" style="border-bottom:1px solid #efefef">
                     <h4 class="pull-left"><?php _e('Tâches en cours d\'exécution', 'domain-manager'); ?></h4>
                  </div>
               </div>
	       <!-- .row -->
	              
               <div class="row">
                  <!-- Stat Boxes widget -->
                  <div class="client-stat-boxes col-md-12 loaded">
	             <div class="widget-content row row-stats">
                        <div class="col-md-3 stat-box-users" ng-repeat="task in runningTasks" >
                           <small><?php _e('De','domain-manager'); ?> {{task._from}} <?php _e('à','domain-manager'); ?> {{task._to.length}} contacts</small>
                           <br/>
                           <span>
                               {{task.position}} <?php _e('sur','domain-manager'); ?> 
                               {{task._to.length}} <?php _e('envoyés','domain-manager'); ?></span>
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
