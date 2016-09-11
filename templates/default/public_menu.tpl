        {if !$public_page}
            <li><a href="{path_for name="fullcard"}">{_T string="Subscribe PDF"}</a></li>
        {else}
            <a id="pfullcard" class="button" href="{path_for name="fullcard"}" title="{_T string="Printable subscription form in PDF"}">{_T string="Subscribe PDF"}</a>
        {/if}
