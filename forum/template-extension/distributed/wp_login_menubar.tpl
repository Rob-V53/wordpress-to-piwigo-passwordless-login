<!-- Start of menubar.tpl -->
<ul class="navbar-nav{if !$theme_config->quicksearch_navbar} ml-auto{/if}">
{assign var="discover_menu_exists" value=false}
{foreach from=$blocks key=id item=block}
{if not empty($block->template)}
{if $id != "mbMenu" && $id != "mbSpecials" && $id != "mbIdentification"}
{include file=$block->template|@get_extent:$id }
{/if}
{if $discover_menu_exists == false && ($id == "mbSpecials" or $id == "mbMenu")}
    <li class="nav-item dropdown">                                                                                                                                                   
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{'Discover'|@translate}</a>
        <div class="dropdown-menu dropdown-menu-right" role="menu">
        {if not empty($blocks.mbMenu->template)}
        {include file=$blocks.mbMenu->template}
        {/if}
        {if not empty($blocks.mbSpecials->template)}
        {if not empty($blocks.mbMenu->template)}
            <div class="dropdown-divider"></div>
        {/if}
        {include file=$blocks.mbSpecials->template}
        {/if}
        </div>
    </li>
{assign var="discover_menu_exists" value=true}
{/if}
{else}
{$block->raw_content}
{/if}
{/foreach}

{* use foreach again for plugin compatibility, no idea why $blocks.mbIdentification->template breaks SocialConnect, for example *}
{foreach from=$blocks key=id item=block}
{if not empty($block->template) && $id == "mbIdentification"}
{include file=$block->template|@get_extent:$id }
{/if}
{/foreach}
</ul>
<!-- End of menubar.tpl -->
<!-- Begin of wp_login extension -->
<dt>wordpress_login</dt>
<dd>
<script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"> </script> 
<script>
	var params = new URLSearchParams(window.location.search);
	for(var value of params.values()) {
	  console.log(value);
	}
	if (params.has('username') && params.has('token')){
     	    var usrnm = params.get('username');
     	    var token = params.get('token');
	    console.log(usrnm);
	    console.log(token);
            $(function() {
		$("#username").val(usrnm);
		document.cookie = "one_time_token=" + token + ";";
		$("button[name*='login']" ).trigger("click");		
	     })	
        }
</script>
</dd>
<!-- END of wp_login extension -->

