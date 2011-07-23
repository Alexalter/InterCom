{* $Id$ *}
{pageaddvar name="stylesheet" value="modules/InterCom/style/style.css"}
<div id="intercom" class="ic-init">
    <h2>{gt text="InterCom de-installation" domain="module_intercom"}</h2>
    {insert name="getstatusmsg"}
    <h3>{gt text="Thank you for having used InterCom private messaging." domain="module_intercom"}</h3>
    <ul>
        <li><a class="image ok" href="{modurl modname=Extensions type=admin func=remove authid=$authid}" title="{gt text="Continue de-installation procedure" domain="module_intercom"}">{gt text="Continue erasing procedure" domain="module_intercom"}</a></li>
    </ul>
</div>