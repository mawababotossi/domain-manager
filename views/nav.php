<?php

function at_display_user_menu(){
$AT_BASE  = plugins_url( '' , __FILE__ );
$SITE_URL = site_url();

$MENU_HTML = "".

"
<div class='sidebar-fixed'> <!-- or .sidebar-fixed -->
   <ul data-highlight-active>
      <li class='active'><a href='#/domains'><i class='icon icon-budicon-377'></i><span>Domains</span></a></li>
      <li><a href='#/applications'>
         <i class='icon icon-budicon-374'></i><span>".__('Applications', 'manager')."</span></a></li>
      <li><a href='#/settings'>
         <i class='icon icon-budicon-329'></i> <span>".__('Règlages', 'manager')."</span></a></li>
     
      <li {M7}><a href='$SITE_URL/profile?profile'>
         <i class='icon icon-budicon-725'></i><span>".__('Mon compte', 'active-texto')."</span></a></li>
    </ul>

    <div class='sidebar-footer-links'></div>
</div>
<!-- .sidebar-fixed -->";

echo $MENU_HTML;
}
