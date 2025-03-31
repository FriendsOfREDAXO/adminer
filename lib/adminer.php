<?php

class rex_adminer extends \Adminer\Adminer
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

    // <<< FIX: Corrected method signature >>>
    function tableStructurePrint($p, $ih = null)
    {
        // Your custom logic to display rex_sql_table code
        if (class_exists(rex_sql_schema_dumper::class) && isset($_GET['table'])) { // Added isset check for safety
            $table = rex_sql_table::get($_GET['table']);
            if ($table) { // Check if table object was successfully retrieved
                $schema = (new rex_sql_schema_dumper())->dumpTable($table);

                // the hightlight() function needs <?php start tag
                // for easier copy (ctrl/cmd + A) we remove the start tag from result
                $code = "<?php \n\n".$schema;
                $code = rex_string::highlight($code);
                $code = str_replace('<?php <br /><br />', '', $code);

                echo '
                    <div style="margin-top: 10px;">
                        <a id="rex-sql-table-code-link" href="#" style="display: block">rex_sql_table code</a>

                        <style type="text/css"'.\Adminer\nonce().'>
                            :root {
                                --code-bg-light: #f5f5f5;
                                --code-border-light: #ddd;
                                --code-text-light: #333;
                                --code-bg-dark: #2d2d2d;
                                --code-border-dark: #555;
                                --code-text-dark: #f0f0f0;
                            }

                            #rex-sql-table-code {
                                border: 1px solid var(--code-border-light);
                                background: var(--code-bg-light);
                                color: var(--code-text-light);
                                padding: 1px 10px 5px 5px;
                                margin-top: 5px;
                                border-radius: 4px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                                transition: all 0.3s ease;
                                position: relative;
                            }

                            #rex-sql-table-code pre {
                                margin-top: 0;
                                padding: 5px;
                            }

                            #rex-sql-table-code code {
                                background: none;
                                font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
                            }

                            /* Dark Mode Toggler */
                            #rex-sql-table-theme-toggle {
                                position: absolute;
                                top: 10px;
                                right: 10px;
                                background: none;
                                border: 1px solid var(--code-border-light);
                                border-radius: 4px;
                                padding: 3px 8px;
                                font-size: 12px;
                                cursor: pointer;
                                transition: all 0.3s ease;
                            }

                            #rex-sql-table-theme-toggle:hover {
                                background-color: rgba(0, 0, 0, 0.05);
                            }

                            /* Dark Mode Class */
                            #rex-sql-table-code.dark-mode {
                                background: var(--code-bg-dark);
                                border-color: var(--code-border-dark);
                                color: var(--code-text-dark);
                            }

                            #rex-sql-table-code.dark-mode #rex-sql-table-theme-toggle {
                                border-color: var(--code-border-dark);
                                color: var(--code-text-dark);
                            }

                            #rex-sql-table-code.dark-mode #rex-sql-table-theme-toggle:hover {
                                background-color: rgba(255, 255, 255, 0.1);
                            }
                        </style>

                        <div id="rex-sql-table-code" class="hidden" contenteditable="true" spellcheck="false">
                            <button id="rex-sql-table-theme-toggle" type="button">Toggle Dark Mode</button>
                            '.$code.'
                        </div>

                        '.\Adminer\script('
                            document.getElementById("rex-sql-table-code-link").addEventListener("click", function () {
                                toggle("rex-sql-table-code");
                                return false;
                            });

                            var code = document.getElementById("rex-sql-table-code");

                            // Verhindern von Bearbeitung
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

                            // Dark Mode Toggle
                            var themeToggle = document.getElementById("rex-sql-table-theme-toggle");
                            if (themeToggle) {
                                themeToggle.addEventListener("click", function (event) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                    code.classList.toggle("dark-mode");

                                    // Speichern der Präferenz in localStorage
                                    if (code.classList.contains("dark-mode")) {
                                        localStorage.setItem("rex_sql_table_theme", "dark");
                                    } else {
                                        localStorage.setItem("rex_sql_table_theme", "light");
                                    }
                                });

                                // Überprüfen gespeicherter Präferenzen
                                if (localStorage.getItem("rex_sql_table_theme") === "dark") {
                                    code.classList.add("dark-mode");
                                }
                            }
                        ').'
                    </div>';
            }
        }

        // <<< FIX: Call the parent method with the correct arguments >>>
        // This ensures the default Adminer structure is still printed after your custom code.
        parent::tableStructurePrint($p, $ih);
    }
}
