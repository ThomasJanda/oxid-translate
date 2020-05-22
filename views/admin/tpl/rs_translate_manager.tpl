[{assign var=step value=$step|default:0}]
[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box=" "}]

<h1>Translation manager</h1>

[{if $step==0}]

    <div style="margin-bottom:20px; ">
        [{oxmultilang ident="rs_translate_manager_step1_description"}]
    </div>
    
    [{assign var=aList value=$oView->getLanguageFilePaths()}]
    [{foreach from=$aList key=sId item=aPaths}]

        <form action="[{$oViewConf->getSelfLink()}]" method="post">
            [{$oViewConf->getHiddenSid()}]
            <input type="hidden" name="cl" value="rs_translate_manager">
            <input type="hidden" name="fnc" value="displayfile">
            <input type="hidden" name="id" value="[{$sId}]">

            [{foreach from=$aPaths key=iLangId item=sPath}]
                [{$sPath}]<br>
            [{/foreach}]
            
            <button class="button" type="submit">[{oxmultilang ident="rs_translate_manager_step1_button_edit"}]</button>
            <hr>
        </form>
    [{/foreach}]
    
[{elseif $step==1}]
   
    <div style="margin-bottom:20px; ">
        [{oxmultilang ident="rs_translate_manager_step2_description"}]
    </div>
    
    <form action="[{$oViewConf->getSelfLink()}]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="rs_translate_manager">

        <button class="button" type="submit">[{oxmultilang ident="rs_translate_manager_step2_button_back"}]</button>
    </form>
    
    [{assign var=aList value=$oView->getLanguageFileContent()}]
    [{assign var=aListSaved value=$oView->getLanguageFileContentSaved()}]
    [{assign var=aLang value=$oView->getLanguageData()}]
    [{assign var=sFileId value=$oView->getFileId()}]
    <table border="0" cellspacing="0" cellpadding="3">
        <tr>
            <th>Key</th>
            [{foreach from=$aLang item=oLang}]
                <th style="color:[{if $oLang->active}]green[{else}]red[{/if}]; "> [{$oLang->name}] ([{$oLang->abbr}])</th>
            [{/foreach}]
            <th></th>
        </tr>
        [{foreach from=$aList key=sLangKey item=aLangValues name="list"}]
            <tr style="border-bottom:1px solid black;">
                <td valign="top">[{$sLangKey}]</td>
                [{foreach from=$aLang item=oLang}]
                    [{assign var=iLangId value=$oLang->id}]
                    [{assign var=v value=$aLangValues.$iLangId}]
                    [{assign var=v_origin value=$v}]
                    [{assign var=bFound value=false}]
                    [{if $aListSaved}]
                        [{foreach from=$aListSaved item=oSave}]
                            [{if $oSave->rs_translate__rs_lang_key->getRawValue()==$sLangKey 
                                && $oSave->rs_translate__rs_lang_id->getRawValue()==$iLangId}]
                                [{assign var=v value=$oSave->rs_translate__rs_lang_value->getRawValue()}]
                                [{assign var=bFound value=true}]
                            [{/if}]
                        [{/foreach}]
                    [{/if}]

                    <td valign="top">
                        <div>
                            <textarea id="textarea[{$smarty.foreach.list.index}]_[{$iLangId}]" form="form[{$smarty.foreach.list.index}]" name="langvalue[[{$iLangId}]]" style="width:200px; height:50px; ">[{$v}]</textarea>
                            <textarea data-origin="textarea[{$smarty.foreach.list.index}]_[{$iLangId}]" style="display:none; ">[{$v}]</textarea>
                        </div>
                        <div class="origin_present" style="display: [{if $bFound}]block[{else}]none[{/if}];">Origin: [{$v_origin}]</div>
                    </td>
                [{/foreach}]
                <td valign="top">
                    <div>
                        <button form="form[{$smarty.foreach.list.index}]" class="button" data-fnc="saveValue" type="button">[{oxmultilang ident="rs_translate_manager_step2_button_save"}]</button>
                    </div>
                    <div class="origin_present" style="display: [{if $bFound}]block[{else}]none[{/if}];">
                        <button form="form[{$smarty.foreach.list.index}]" class="button" data-fnc="deleteValue" type="button">[{oxmultilang ident="rs_translate_manager_step2_button_delete"}]</button>
                    </div>
                    <form id="form[{$smarty.foreach.list.index}]" action="[{$oViewConf->getSelfLink()}]" method="post">
                        [{$oViewConf->getHiddenSid()}]
                        <input type="hidden" name="cl" value="rs_translate_manager">
                        <input type="hidden" name="langkey" value="[{$sLangKey}]">
                        <input type="hidden" name="fileid" value="[{$sFileId}]">
                        <input type="hidden" name="fnc" value="">
                    </form>
                </td>
            </tr>
        [{/foreach}]
    </table>

    <form action="[{$oViewConf->getSelfLink()}]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="rs_translate_manager">

        <button class="button" type="submit">[{oxmultilang ident="rs_translate_manager_step2_button_back"}]</button>
    </form>
    
    <script
        src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>
    <script>
        $('button[data-fnc="saveValue"]').click(function() {
            let button = $(this);
            let formid = $(this).attr('form');
            let form = $('form#' + formid);
            form.find('input[type="hidden"][name="fnc"]').val('saveValue');
            let url = form.attr('action');
            let data = form.serialize();
            
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                success: function() {
                    button.closest('tr').find('.origin_present').css('display','block');
                }
              });
        });
        $('button[data-fnc="deleteValue"]').click(function() {
            let button = $(this);
            let formid = $(this).attr('form');
            let form = $('form#' + formid);
            form.find('input[type="hidden"][name="fnc"]').val('deleteValue');
            let url = form.attr('action');
            let data = form.serialize();
            
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                success: function() {
                    let tr = button.closest('tr');
                    tr.find('.origin_present').css('display','none');
                    
                    tr.find('textarea[data-origin]').each(function() {
                        let v = $(this).val();
                        let id = $(this).attr('data-origin');
                        $('textarea#' + id).val(v);
                    });
                }
              });
        });
    </script>

[{/if}]

    <div style="height:50px; "></div>
</body>
</html>