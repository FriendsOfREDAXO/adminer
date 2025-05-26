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
                $code = str_replace('<?php <br /><br />', '', $code);

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

                            /* Button Container */
                            .rex-sql-table-buttons {
                                position: absolute;
                                top: 10px;
                                right: 10px;
                                display: flex;
                                gap: 5px;
                            }

                            /* Button Styles */
                            #rex-sql-table-theme-toggle,
                            #rex-sql-table-copy-button {
                                background: #1e3a8a;
                                border: 1px solid #1e40af;
                                border-radius: 4px;
                                padding: 5px 10px;
                                font-size: 12px;
                                color: #ffffff;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                font-weight: 500;
                            }

                            #rex-sql-table-theme-toggle:hover,
                            #rex-sql-table-copy-button:hover {
                                background: #1e40af;
                                transform: translateY(-1px);
                            }

                            /* Dark Mode Class */
                            #rex-sql-table-code.dark-mode {
                                background: var(--code-bg-dark);
                                border-color: var(--code-border-dark);
                                color: var(--code-text-dark);
                            }

                            #rex-sql-table-code.dark-mode pre {
                                color: var(--code-text-dark);
                            }

                            #rex-sql-table-code.dark-mode code {
                                color: var(--code-text-dark);
                            }

                            #rex-sql-table-code.dark-mode #rex-sql-table-theme-toggle,
                            #rex-sql-table-code.dark-mode #rex-sql-table-copy-button {
                                background: #3b82f6;
                                border-color: #60a5fa;
                                color: #ffffff;
                            }

                            #rex-sql-table-code.dark-mode #rex-sql-table-theme-toggle:hover,
                            #rex-sql-table-code.dark-mode #rex-sql-table-copy-button:hover {
                                background: #60a5fa;
                            }
                        </style>

                        <div id="rex-sql-table-code" class="hidden" contenteditable="true" spellcheck="false">
                            <div class="rex-sql-table-buttons">
                                <button id="rex-sql-table-copy-button" type="button" title="Code in Zwischenablage kopieren">📋 Kopieren</button>
                                <button id="rex-sql-table-theme-toggle" type="button" title="Dark/Light Mode umschalten">🌙 Dark</button>
                            </div>
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

                                    // Button Text und Icon anpassen
                                    if (code.classList.contains("dark-mode")) {
                                        themeToggle.innerHTML = "☀️ Light";
                                        localStorage.setItem("rex_sql_table_theme", "dark");
                                    } else {
                                        themeToggle.innerHTML = "🌙 Dark";
                                        localStorage.setItem("rex_sql_table_theme", "light");
                                    }
                                });

                                // Überprüfen gespeicherter Präferenzen
                                if (localStorage.getItem("rex_sql_table_theme") === "dark") {
                                    code.classList.add("dark-mode");
                                    themeToggle.innerHTML = "☀️ Light";
                                } else {
                                    themeToggle.innerHTML = "🌙 Dark";
                                }
                            }

                            // Copy Button
                            var copyButton = document.getElementById("rex-sql-table-copy-button");
                            if (copyButton) {
                                copyButton.addEventListener("click", function (event) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                    
                                    try {
                                        // Text aus dem Code-Bereich extrahieren (ohne HTML)
                                        var codeElement = code.querySelector("pre");
                                        if (!codeElement) {
                                            // Fallback: gesamten Text-Inhalt verwenden
                                            var tempDiv = document.createElement("div");
                                            tempDiv.innerHTML = code.innerHTML;
                                            // Buttons entfernen
                                            var buttons = tempDiv.querySelector(".rex-sql-table-buttons");
                                            if (buttons) {
                                                buttons.remove();
                                            }
                                            var textContent = tempDiv.textContent || tempDiv.innerText;
                                        } else {
                                            var textContent = codeElement.textContent || codeElement.innerText;
                                        }
                                        
                                        // In Zwischenablage kopieren
                                        if (navigator.clipboard && window.isSecureContext) {
                                            navigator.clipboard.writeText(textContent).then(function() {
                                                // Erfolgsmeldung
                                                var originalText = copyButton.innerHTML;
                                                copyButton.innerHTML = "✅ Kopiert!";
                                                copyButton.style.background = "#10b981";
                                                setTimeout(function() {
                                                    copyButton.innerHTML = originalText;
                                                    copyButton.style.background = "";
                                                }, 2000);
                                            }).catch(function(err) {
                                                console.error("Fehler beim Kopieren: ", err);
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
                                                copyButton.innerHTML = "✅ Kopiert!";
                                                copyButton.style.background = "#10b981";
                                                setTimeout(function() {
                                                    copyButton.innerHTML = originalText;
                                                    copyButton.style.background = "";
                                                }, 2000);
                                            } catch (err) {
                                                console.error("Fallback Kopieren fehlgeschlagen: ", err);
                                                alert("Kopieren fehlgeschlagen. Bitte manuell markieren und kopieren.");
                                            } finally {
                                                document.body.removeChild(textArea);
                                            }
                                        }
                                        
                                    } catch (error) {
                                        console.error("Fehler beim Kopieren: ", error);
                                        alert("Fehler beim Kopieren. Bitte manuell markieren und kopieren.");
                                    }
                                });
                            }
                        ').'
                    </div>';
            }
        }

        // <<< FIX: Call the parent method with the correct arguments >>>
        // This ensures the default Adminer structure is still printed after your custom code.
        parent::tableStructurePrint($p, $ih);
    }
    
    // New dark mode functions
    function head($dark = null) {
        ?>
<style <?php echo Adminer\nonce(); ?>>
#dark-mode-toggle {
    position: fixed;
    bottom: 1.5em;
    right: 1.5em;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f0f0f0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1000;
    transition: background-color 0.3s, transform 0.2s;
}

#dark-mode-toggle:hover {
    transform: scale(1.1);
}

.dark-mode-active #dark-mode-toggle {
    background-color: #333;
}

#dark-mode-toggle .icon {
    font-size: 24px;
    transition: opacity 0.3s;
}

#dark-mode-toggle .dark-icon {
    display: none;
    }

    .dark-mode-active #dark-mode-toggle .light-icon {
        display: none;
    }

    .dark-mode-active #dark-mode-toggle .dark-icon {
        display: block;
    }
</style>
<script <?php echo Adminer\nonce(); ?>>
    let adminerDark;
    function adminerDarkSwitch() {
        adminerDark = !adminerDark;
        adminerDarkSet();
    }
    function adminerDarkSet() {
        qsa('link[href*="dark.css"]').forEach(link => link.media = (adminerDark ? '' : 'never'));
        qs('meta[name="color-scheme"]').content = (adminerDark ? 'dark' : 'light');
        cookie('adminer_dark=' + (adminerDark ? 1 : 0), 30);

        // Toggle body class for our dark mode toggle styles
        if (adminerDark) {
            document.body.classList.add('dark-mode-active');
        } else {
            document.body.classList.remove('dark-mode-active');
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

    function navigation($missing) {
        echo '<div id="dark-mode-toggle">
                <span class="icon light-icon">☀️</span>
                <span class="icon dark-icon">🌙</span>
              </div>'
            . Adminer\script("
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
