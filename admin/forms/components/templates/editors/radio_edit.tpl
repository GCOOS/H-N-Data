<div
    data-editor="true"
    data-editor-class="RadioGroup"
    data-editor-name-"{$RadioEdit->GetName()}"
    data-field-name="{$RadioEdit->GetFieldName()}"
    data-editable="{if $RadioEdit->GetReadOnly()}false{else}true{/if}"
    id="{$RadioEdit->GetName()}"
    {style_block}
        {$RadioEdit->GetCustomAttributes()}
    {/style_block}
    >
{if !$RadioEdit->GetReadOnly()}
{foreach key=Value item=Name from=$RadioEdit->GetValues()}

<label class="radio"><input name="{$RadioEdit->GetName()}" value="{$Value}"{if $RadioEdit->GetSelectedValue() eq $Value} checked="checked"{/if} type="radio" {$Validators.InputAttributes}>{$Name}</label>

{/foreach}
{else}
{foreach key=Value item=Name from=$RadioEdit->GetValues()}
{if $RadioEdit->GetSelectedValue() eq $Value}{$Name}{/if}
{/foreach}
{/if}
</div>
