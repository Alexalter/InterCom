{adminheader}
{pageaddvar name="stylesheet" value="modules/InterCom/style/style.css"}
<div id="intercom" class="ic-init">
    <h2>{gt text="InterCom installation" domain="module_intercom"}</h2>
    {insert name="getstatusmsg"}
    <p class="z-informationmsg">{gt text="You have now completed the InterCom installation procedure." domain="module_intercom"}</p>
    <form class="z-form" action="{modurl modname="Extensions" type="admin" func="setState"}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <fieldset>
                <legend>{gt text="Finalisation" domain="module_intercom"}</legend>
                <div class="z-formrow">
                    <label for="activate">{gt text="Activate InterCom" domain="module_intercom"}</label>
                    <input id="activate" name="active" type="checkbox" value="1" />
                </div>
            </fieldset>
            <div class="z-formbuttons">
                {button src=button_ok.gif set=icons/small __alt="OK" __title="OK"}
            </div>
        </div>
    </form>
</div>