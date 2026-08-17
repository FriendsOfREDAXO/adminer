<?php

namespace FriendsOfRedaxo\Adminer;

use rex_addon;
use rex_sql_schema_dumper;
use rex_sql_table;
use rex_string;

class Adminer extends \Adminer\Adminer
{
    private $markYFormTables = false;

    private $yformTableNames = [];

    public function credentials()
    {
        $db = rex_addon::get('adminer')->getProperty('database');

        return [$db['host'], $db['login'], $db['password']];
    }

    public function login($login, $password)
    {
        return true;
    }

    public function databases($flush = true)
    {
        $databases = [];

        foreach (rex_addon::get('adminer')->getProperty('databases') as $db) {
            $databases[$db['name']] = $db['name'] . ' (' . $db['host'] . ')';
        }

        return $databases;
    }

    public function databasesPrint($missing)
    {
        if (\count(rex_addon::get('adminer')->getProperty('databases')) <= 1) {
            return;
        }

        parent::databasesPrint($missing);
    }

    public function tablesPrint(array $tables)
    {
        $user = \rex::getUser();
        $this->markYFormTables = rex_addon::get('yform')->isAvailable()
            && $user
            && $user->isAdmin()
            && class_exists(\rex_yform_manager_table::class);

        if ($this->markYFormTables) {
            $this->yformTableNames = array_fill_keys(array_keys(\rex_yform_manager_table::getAll()), true);
        }

        parent::tablesPrint($tables);
        $this->markYFormTables = false;
    }

    public function tableName(array $tableStatus)
    {
        $name = parent::tableName($tableStatus);
        if ($this->markYFormTables && isset($this->yformTableNames[$tableStatus['Name']])) {
            $name = '<span class="rex-adminer-yform-badge" title="' . \rex_escape(\rex_i18n::msg('adminer_yform_badge_title')) . '">YForm</span> ' . $name;
        }

        return $name;
    }

    // <<< FIX: Corrected method signature >>>
    public function tableStructurePrint($p, $ih = null)
    {
        // Your custom logic to display rex_sql_table code
        if (class_exists(rex_sql_schema_dumper::class) && isset($_GET['table'])) { // Added isset check for safety
            $table = rex_sql_table::get($_GET['table']);
            if ($table) { // Check if table object was successfully retrieved
                $schema = (new rex_sql_schema_dumper())->dumpTable($table);

                // the hightlight() function needs <?php start tag
                // for easier copy (ctrl/cmd + A) we remove the start tag from result
                $code = "<?php \n\n" . $schema;
                $code = rex_string::highlight($code);
                $code = str_replace('<?php <br /><br />', '', $code);

                $tableName = $table->getName();
                $tableReference = var_export($tableName, true);
                if (0 === strpos($tableName, \rex::getTablePrefix())) {
                    $tableReference = 'rex::getTable(' . var_export(substr($tableName, strlen(\rex::getTablePrefix())), true) . ')';
                }

                $dropCode = "<?php \n\nrex_sql_table::get(" . $tableReference . ")->drop();\n";
                $dropCode = rex_string::highlight($dropCode);
                $dropCode = str_replace('<?php <br /><br />', '', $dropCode);

                $copyLabel = \rex_i18n::msg('adminer_copy');
                $copiedLabel = json_encode(\rex_i18n::msg('adminer_copied'));
                $copyError = json_encode(\rex_i18n::msg('adminer_copy_error'));
                $copyConsoleError = json_encode(\rex_i18n::msg('adminer_copy_console_error'));
                $copyFallbackConsoleError = json_encode(\rex_i18n::msg('adminer_copy_fallback_console_error'));

                $yformNotice = '';
                $user = \rex::getUser();
                if (rex_addon::get('yform')->isAvailable() && $user && $user->isAdmin() && class_exists(\rex_yform_manager_table::class)) {
                    $yformTable = \rex_yform_manager_table::get($tableName);
                    if ($yformTable) {
                        $yformConfigUrl = \rex_url::backendPage('yform/manager/table_field', ['table_name' => $tableName], false);
                        $yformNotice = '
                            <div class="rex-adminer-yform-notice" role="note">
                                <div>' . \rex_escape(\rex_i18n::msg('adminer_yform_notice')) . '</div>
                                <a class="rex-adminer-yform-button" href="' . \rex_escape($yformConfigUrl) . '" target="_blank" rel="noopener">' . \rex_escape(\rex_i18n::msg('adminer_yform_config_open')) . '</a>
                            </div>';
                    }
                }

                echo '
                    <div style="margin-top: 10px;">
                        ' . $yformNotice . '
                        <div><a id="rex-sql-table-code-link" href="#">' . \rex_escape(\rex_i18n::msg('adminer_sql_table_code')) . '</a></div>

                        <style type="text/css"' . \Adminer\nonce() . '>
                            :root {
                                --code-bg-light: #f5f5f5;
                                --code-border-light: #ddd;
                                --code-text-light: #333;
                                --code-bg-dark: #2d2d2d;
                                --code-border-dark: #555;
                                --code-text-dark: #eee;
                            }

                            #rex-sql-table-code {
                                border: 1px solid var(--code-border-light);
                                background: var(--code-bg-light);
                                color: var(--code-text-light);
                                padding: 1px 10px 5px 5px;
                                margin-top: 5px;
                                border-radius: 4px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                                transition: box-shadow 0.3s ease;
                                position: relative;
                            }

                            .rex-adminer-yform-notice {
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                gap: 16px;
                                margin: 0 0 10px;
                                padding: 10px 12px;
                                border: 1px solid rgb(75, 154, 217);
                                border-left-width: 4px;
                                border-radius: 4px;
                                background: #2f78b1;
                                color: #fff;
                            }

                            .rex-adminer-yform-button {
                                display: inline-block;
                                flex: 0 0 auto;
                                padding: 7px 10px;
                                border: 1px solid #2d3a48;
                                border-radius: 4px;
                                background: #3c4d60;
                                color: #fff !important;
                                font-weight: 700;
                                text-decoration: none;
                                white-space: nowrap;
                            }

                            .rex-adminer-yform-button:hover,
                            .rex-adminer-yform-button:focus {
                                background: #293746;
                                color: #fff !important;
                            }

                            .dark-mode-active .rex-adminer-yform-notice {
                                border-color: rgb(75, 154, 217);
                                background: #244a68;
                                color: #e7f4ff;
                            }

                            .dark-mode-active .rex-adminer-yform-button {
                                border-color: #7893ad;
                                background: #536a83;
                            }

                            .dark-mode-active .rex-adminer-yform-button:hover,
                            .dark-mode-active .rex-adminer-yform-button:focus {
                                background: #67819b;
                            }

                            @media (max-width: 700px) {
                                .rex-adminer-yform-notice {
                                    align-items: stretch;
                                    flex-direction: column;
                                }

                                .rex-adminer-yform-button {
                                    text-align: center;
                                    white-space: normal;
                                }
                            }

                            #rex-sql-table-code pre {
                                margin-top: 0;
                                padding: 5px;
                            }

                            #rex-sql-table-code code {
                                background: none;
                                font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
                            }

                            /* Button Container */
                            .rex-sql-table-buttons {
                                position: absolute;
                                top: 10px;
                                right: 10px;
                                display: flex;
                                gap: 5px;
                            }

                            /* Button Styles */
                            #rex-sql-table-mode-toggle,
                            #rex-sql-table-copy-button {
                                background: #3c4d60;
                                border: 1px solid #2d3a48;
                                border-radius: 4px;
                                padding: 5px 10px;
                                font-size: 12px;
                                color: #ffffff;
                                cursor: pointer;
                                transition: transform 0.2s ease;
                                font-weight: 500;
                            }

                            #rex-sql-table-mode-toggle:hover,
                            #rex-sql-table-copy-button:hover {
                                background: #293746;
                                transform: translateY(-1px);
                            }

                            /* Dark Mode Class */
                            .dark-mode-active #rex-sql-table-code {
                                background-color: var(--code-bg-dark) !important;
                                border-color: var(--code-border-dark) !important;
                                color: var(--code-text-dark);
                            }

                            .dark-mode-active #rex-sql-table-code pre,
                            .dark-mode-active #rex-sql-table-code code,
                            .dark-mode-active #rex-sql-table-code code * {
                                color: var(--code-text-dark) !important;
                            }

                            .dark-mode-active #rex-sql-table-code #rex-sql-table-mode-toggle,
                            .dark-mode-active #rex-sql-table-code #rex-sql-table-copy-button {
                                background: #536a83;
                                border-color: #7893ad;
                                color: #ffffff;
                            }

                            .dark-mode-active #rex-sql-table-code #rex-sql-table-mode-toggle:hover,
                            .dark-mode-active #rex-sql-table-code #rex-sql-table-copy-button:hover {
                                background: #67819b;
                            }

                            .rex-sql-table-snippet.hidden {
                                display: none;
                            }
                        </style>

                        <div id="rex-sql-table-code" class="hidden" contenteditable="true" spellcheck="false">
                            <div class="rex-sql-table-buttons">
                                <button id="rex-sql-table-copy-button" type="button" title="' . \rex_escape(\rex_i18n::msg('adminer_copy_title')) . '">📋 ' . \rex_escape($copyLabel) . '</button>
                                <button id="rex-sql-table-mode-toggle" type="button" title="' . \rex_escape(\rex_i18n::msg('adminer_code_mode_title')) . '">uninstall.php</button>
                            </div>
                            <div id="rex-sql-table-install-code" class="rex-sql-table-snippet">' . $code . '</div>
                            <div id="rex-sql-table-uninstall-code" class="rex-sql-table-snippet hidden">' . $dropCode . '</div>
                        </div>

                        ' . \Adminer\script('
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

                            // Install/Uninstall Toggle
                            var modeToggle = document.getElementById("rex-sql-table-mode-toggle");
                            var installCode = document.getElementById("rex-sql-table-install-code");
                            var uninstallCode = document.getElementById("rex-sql-table-uninstall-code");
                            if (modeToggle) {
                                modeToggle.addEventListener("click", function (event) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                    installCode.classList.toggle("hidden");
                                    uninstallCode.classList.toggle("hidden");
                                    modeToggle.textContent = uninstallCode.classList.contains("hidden") ? "uninstall.php" : "install.php";
                                });
                            }

                            // Copy Button
                            var copyButton = document.getElementById("rex-sql-table-copy-button");
                            if (copyButton) {
                                copyButton.addEventListener("click", function (event) {
                                    event.preventDefault();
                                    event.stopPropagation();

                                    try {
                                        // Text aus dem Code-Bereich extrahieren (ohne HTML)
                                        var activeSnippet = code.querySelector(".rex-sql-table-snippet:not(.hidden)");
                                        var codeElement = activeSnippet.querySelector("pre");
                                        if (!codeElement) {
                                            // Fallback: gesamten Text-Inhalt verwenden
                                            var tempDiv = document.createElement("div");
                                            tempDiv.innerHTML = activeSnippet.innerHTML;
                                            var textContent = tempDiv.textContent || tempDiv.innerText;
                                        } else {
                                            var textContent = codeElement.textContent || codeElement.innerText;
                                        }

                                        // In Zwischenablage kopieren
                                        if (navigator.clipboard && window.isSecureContext) {
                                            navigator.clipboard.writeText(textContent).then(function() {
                                                // Erfolgsmeldung
                                                var originalText = copyButton.innerHTML;
                                                copyButton.innerHTML = "✅ " + ' . $copiedLabel . ';
                                                copyButton.style.background = "#2f78b1";
                                                setTimeout(function() {
                                                    copyButton.innerHTML = originalText;
                                                    copyButton.style.background = "";
                                                }, 2000);
                                            }).catch(function(err) {
                                                console.error(' . $copyConsoleError . ', err);
                                                fallbackCopy(textContent);
                                            });
                                        } else {
                                            // Fallback für ältere Browser
                                            fallbackCopy(textContent);
                                        }

                                        function fallbackCopy(text) {
                                            var textArea = document.createElement("textarea");
                                            textArea.value = text;
                                            textArea.style.position = "fixed";
                                            textArea.style.left = "-999999px";
                                            textArea.style.top = "-999999px";
                                            document.body.appendChild(textArea);
                                            textArea.focus();
                                            textArea.select();

                                            try {
                                                document.execCommand("copy");
                                                var originalText = copyButton.innerHTML;
                                                copyButton.innerHTML = "✅ " + ' . $copiedLabel . ';
                                                copyButton.style.background = "#2f78b1";
                                                setTimeout(function() {
                                                    copyButton.innerHTML = originalText;
                                                    copyButton.style.background = "";
                                                }, 2000);
                                            } catch (err) {
                                                console.error(' . $copyFallbackConsoleError . ', err);
                                                alert(' . $copyError . ');
                                            } finally {
                                                document.body.removeChild(textArea);
                                            }
                                        }

                                    } catch (error) {
                                        console.error(' . $copyConsoleError . ', error);
                                        alert(' . $copyError . ');
                                    }
                                });
                            }
                        ') . '
                    </div>';
            }
        }

        // <<< FIX: Call the parent method with the correct arguments >>>
        // This ensures the default Adminer structure is still printed after your custom code.
        parent::tableStructurePrint($p, $ih);
    }

    // New dark mode functions
    public function head($dark = null)
    {
        ?>
<style <?= \Adminer\nonce() ?>>
#dark-mode-toggle {
    position: fixed;
    bottom: 1.5em;
    right: 1.5em;
    width: 40px;
    height: 40px;
    padding: 0;
    border: 1px solid rgb(75, 154, 217);
    border-radius: 50%;
    background-color: #3c4d60;
    color: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1000;
    transition: transform 0.2s;
}

#dark-mode-toggle:hover {
    background-color: #2f78b1;
    transform: scale(1.1);
}

.dark-mode-active #dark-mode-toggle {
    border-color: #7893ad;
    background-color: #536a83;
}

.dark-mode-active #dark-mode-toggle:hover {
    background-color: #67819b;
}

#tables .rex-adminer-yform-badge {
    display: inline-block;
    margin-right: 4px;
    padding: 1px 4px;
    border-radius: 3px;
    background: #2f78b1;
    box-shadow: inset 0 0 0 1px rgb(75, 154, 217);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    line-height: 1.3;
    vertical-align: 1px;
}

.dark-mode-active #tables .rex-adminer-yform-badge {
    background: #536a83;
    color: #fff;
}

#dark-mode-toggle .icon {
    display: block;
    font-family: Georgia, serif;
    font-size: 25px;
    line-height: 1;
    transition: opacity 0.3s;
}

#dark-mode-toggle .theme-icon-light {
    display: none;
}

.dark-mode-active #dark-mode-toggle .theme-icon-dark {
    display: none;
}

.dark-mode-active #dark-mode-toggle .theme-icon-light {
    display: block;
}
</style>
<script <?= \Adminer\nonce() ?>>
    let adminerDark;
    function adminerDarkSwitch() {
        adminerDark = !adminerDark;
        adminerDarkSet();
    }
    function adminerDarkSet() {
        qsa('link[href*="dark.css"]').forEach(link => link.media = (adminerDark ? '' : 'never'));
        qs('meta[name="color-scheme"]').content = (adminerDark ? 'dark' : 'light');
        cookie('adminer_dark=' + (adminerDark ? 1 : 0), 30);

        // The initial call runs in <head>; navigation calls it again after <body> exists.
        if (document.body) {
            document.body.classList.toggle('dark-mode-active', !!adminerDark);
        }
    }
    const saved = document.cookie.match(/adminer_dark=(\d)/);
    if (saved) {
        adminerDark = +saved[1];
        adminerDarkSet();
    }
</script>
<?php
        // Call parent method if it exists
        if (method_exists(get_parent_class($this), 'head')) {
            parent::head($dark);
        }
    }

    public function navigation($missing)
    {
        $themeToggleTitle = \rex_escape(\rex_i18n::msg('adminer_theme_toggle_title'));
        echo '<button id="dark-mode-toggle" type="button" title="' . $themeToggleTitle . '" aria-label="' . $themeToggleTitle . '">
            <span class="icon theme-icon-dark" aria-hidden="true">☾</span>
            <span class="icon theme-icon-light" aria-hidden="true">☼</span>
              </button>'
            . \Adminer\script("
                if (adminerDark != null) {
                    adminerDarkSet();
                }

                qs('#dark-mode-toggle').onclick = function() {
                    adminerDarkSwitch();
                };
            ") . "\n"
            ;

        // Call parent method to ensure default navigation is still displayed
        parent::navigation($missing);
    }
}
