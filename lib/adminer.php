<?php

class rex_adminer extends Adminer
{
    function credentials()
    {
        $db = rex_addon::get('adminer')->getProperty('database');

        return [$db['host'], $db['login'], $db['password']];
    }

    function login($login, $password)
    {
        return true;
    }

    function databases($flush = true)
    {
        $databases = [];

        foreach (rex_addon::get('adminer')->getProperty('databases') as $db) {
            $databases[$db['name']] = $db['name'].' ('. $db['host'] .')';
        }

        return $databases;
    }

    function databasesPrint($missing)
    {
        if (count(rex_addon::get('adminer')->getProperty('databases')) <= 1) {
            return;
        }

        parent::databasesPrint($missing);
    }

    function tableStructurePrint($fields)
    {
        if (class_exists(rex_sql_schema_dumper::class)) {
            $table = rex_sql_table::get($_GET['table']);
            $schema = (new rex_sql_schema_dumper())->dumpTable($table);

            // the hightlight() function needs <?php start tag
            // for easier copy (ctrl/cmd + A) we remove the start tag from result
            $code = "<?php \n\n".$schema;
            $code = rex_string::highlight($code);
            $code = str_replace('&lt;?php&nbsp;<br /><br />', '', $code);

            echo '
                <div style="margin-top: 10px;">
                    <a id="rex-sql-table-code-link" href="#" style="display: block">rex_sql_table code</a>

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

                    <div id="rex-sql-table-code" class="hidden" contenteditable="true" spellcheck="false">
                        '.$code.'
                    </div>

                    <script '.nonce().'>
                        document.getElementById("rex-sql-table-code-link").addEventListener("click", function () {
                            toggle("rex-sql-table-code");
                            return false;
                        });

                        var code = document.getElementById("rex-sql-table-code");
                        code.addEventListener("cut", function (event) {
                            event.preventDefault();
                        });
                        code.addEventListener("paste", function (event) {
                            event.preventDefault();
                        });
                        code.addEventListener("keydown", function (event) {
                            if (!event.metaKey) {
                                event.preventDefault();
                            }
                        });
                    </script>
                </div>';
        }

        parent::tableStructurePrint($fields);
    }
}
