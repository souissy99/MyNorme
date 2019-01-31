<?php

$nb_funct = 0;

function line_jump_declaration($reading, &$lineNumber)
{
    global $error;
    if (preg_match("/\w+ \w+;/", $reading) == 0){
        if (preg_match("/\w+ \w+;\n\w+ \w+;/", $reading) != 0) {
            }
        elseif (preg_match("/\w+ \w+;\n\n/", $reading) != 0) {
        }
        elseif (preg_match("/\w+ \w+;\n./", $reading) != 0){
            $error = $error + 1;
            echo "\033[31mErreur \033[0m: \033[34mligne " . $lineNumber;
            echo "\033[0m: Il n\'y\'a pas de saut de ligne après la déclaration" . "\n"; 
        }
    }
}

function tab_declaration($reading, &$lineNumber)
{
    global $error;
    if (preg_match("/\w+ +\w+;/", $reading) != 0) {
        $error = $error + 1;
        echo "\033[31mErreur \033[0m: \033[34mligne " . $lineNumber;
        echo "\033[0m: Il n'y a pas de tabulation dans la déclaration" . "\n";
    }
}

function declaration_affectation($reading, &$lineNumber)
{
    global $error;
    $pattern = '/\w+\s+\w+(\s+)?=(\s+)?(\'|\")?\w+(\'|")?(\s+)?;(\s+)?$/';
    if (preg_match($pattern, $reading) != 0) {
        $error = $error + 1;
        echo "\033[31mErreur \033[0m: \033[34mligne " . $lineNumber;
        echo "\033[0m: Il y'a une déclaration et une affectation sur la même ligne" . "\n";
    }
}

function keyword_space($reading, &$lineNumber)
{
    global $error;
    $keyword = array('for', 'while', 'if', 'do', 'signed', 'unsigned', 'void', 'int', 'char', 'const', 'float', 'double', 'extern', 'else', 'return', 'short', 'long', 'register', 'sizeof', 'static', 'case', 'switch', 'typedef', 'union', 'volatile');
    $j = 0;
    while (isset($keyword[$j])) {
        if (preg_match("/$keyword[$j]\S/", $reading) != 0) {
            $error = $error + 1;
            echo "\033[31mErreur \033[0m: \033[34mligne " . $lineNumber;
            echo "\033[0m: Il manque un espace après un mot clé" . "\n";
        }
        $j = $j + 1;
    }
}

function nb_params($reading, &$lineNumber)
{
    global $error;
    if (preg_match("/\w+\s+\w+\(/", $reading) != 0)
    {
        if (preg_match_all("/\w+ \w+, \w+ \w+, \w+ \w+, \w+ \w+,/", $reading) != 0) {
            echo "\033[31mErreur \033[0m: \033[34mligne " . $lineNumber;
            echo "\033[0m: Il y'a plus de 4 paramètres dans votre fonction" . "\n";
            $error = $error + 1;
        }
    }
}

function nb_function($reading, &$nb_funct, &$lineNumber)
{
    global $error;
    if (preg_match("/\w+\s+\w+\(/", $reading) != 0) {
        $nb_funct++;
        if ($nb_funct > 5) {
            $error = $error + 1;
            echo "\033[31mErreur \033[0m: \033[34mligne " . $lineNumber;
            echo "\033[0m: Il y'a plus de 5 fonctions dans votre de fichier" . "\n";
        }

    }
}

function space_end($reading, &$lineNumber)
{
    global $error;
    if (preg_match("/ \s+$/", $reading) != 0) {
        $error = $error + 1;
        echo "\033[31mErreur \033[0m: \033[34mligne " . $lineNumber;
        echo "\033[0m: Il y'a un espace en fin de ligne" . "\n";
    }

}

function nb_char_ligne($reading, &$lineNumber)
{
    global $error;
    $nb_char = strlen($reading);
    if ($nb_char > 81) {
        $ft = $nb_char - 81;
        $error = $error + $ft;
        echo "\033[31mErreur \033[0m: \033[34mligne " . $lineNumber;
        echo "\033[0m: Il y'a plus de 80 caractères ligne" . "\n";
    }
}

global $error;
$dir = $argv[1];
$i = 2;

if (file_exists($dir)) {
    $file = scandir($dir);
    if (count($file) == 2) {
        echo "\033[31m/!\ \033[0mLe dossier est vide" . "\n";
    } else {
        while (isset($file[$i])) {
            echo 'Scan ';
            print_r($file[$i]);
            echo "\n";
            $lines = file("$dir/$file[$i]");
            foreach ($lines as $lineNumber => $lineContent) {
                $lineNumber = $lineNumber + 1;
                nb_char_ligne($lineContent, $lineNumber);
                keyword_space($lineContent, $lineNumber);
                nb_function($lineContent, $nb_funct, $lineNumber);
                space_end($lineContent, $lineNumber);
                declaration_affectation($lineContent, $lineNumber);
                tab_declaration($lineContent, $lineNumber);
                line_jump_declaration($lineContent, $lineNumber);
                nb_params($lineContent, $lineNumber);

            }
            $nb_funct = 0;
            $i = $i + 1;
        }
        if ($error == 0)
            echo 'Vous n\'avez pas d\'erreurs' . "\n";
        else
            echo "Vous avez \033[31m $error erreurs\033[0m de normes au total.\n";
    }
} else {
    echo "\033[31m/!\ \033[0mLe dossier n'existe pas" . "\n";
}
?>
