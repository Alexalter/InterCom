{adminheader}
{pageaddvar name="stylesheet" value="modules/InterCom/style/style.css"}
<div id="intercom" class="ic-init">
    <h2>{gt text="InterCom installation" domain="module_intercom"}</h2>
    {insert name="getstatusmsg"}
    <h3>{gt text="Step 1 of InterCom installation procedure" domain="module_intercom"}</h3>
    <ul>
        <li><a class="image ok" href="{modurl modname="InterCom" type="interactiveinstaller" func="step2"}" title="{gt text="Begin installation" domain="module_intercom"}">{gt text="Commence installation" domain="module_intercom"}</a></li>
    </ul>
</div>