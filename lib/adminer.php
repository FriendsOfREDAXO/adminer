<?php

class rex_adminer extends Adminer
{
    function credentials()
    {
        $db = rex::getProperty('db')[1];
        return [$db['host'], $db['login'], $db['password']];
    }

    function database()
    {
        return rex::getProperty('db')[1]['name'];
    }

    function databases($flush = true)
    {
        return [];
    }

    function databasesPrint($missing)
    {
    }

    function tableStructurePrint($fields)
    {
        $table = rex_sql_table::get($_GET['table']);
        $schema = (new rex_sql_schema_dumper())->dumpTable($table);

        // the hightlight() function needs <?php start tag
        // for easier copy (ctrl/cmd + A) we remove the start tag from result
        $code = "<?php \n\n".$schema;
        $code = rex_string::highlight($code);
        $code = str_replace('&lt;?php&nbsp;<br /><br />', '', $code);

        echo '
            <div style="margin-top: 10px;">
                <a href="#" onclick="return !toggle(\'rex-sql-table-code\')" style="display: block">rex_sql_table code</a>
                
                <style type="text/css">
                    #rex-sql-table-code {
                        border: 1px solid #999; 
                        background: #eee;
                        padding: 1px 10px 5px 5px; 
                        margin-top: 5px;
                    }
                    
                    #rex-sql-table-code pre {
                        margin-top: 0;
                    }
                    
                    #rex-sql-table-code code {
                        background: none;
                    }
                </style>
                
                <div id="rex-sql-table-code" class="hidden" 
                    contenteditable="true"
                    oncut="return false"
                    onpaste="return false"
                    onkeydown="return event.metaKey"
                >
                    '.$code.'
                </div>
            </p>';

        parent::tableStructurePrint($fields);
    }
}
