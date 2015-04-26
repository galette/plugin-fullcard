        {if $public_page}
            <a id="pfullcard" class="button{if $PAGENAME eq "fullcard.php"} selected{/if}" href="{$galette_base_path}{$galette_galette_fullcard_path}fullcard.php" title="{_T string="Printable subscription form in PDF"}">{_T string="Subscribe PDF"}</a>
        {/if}
