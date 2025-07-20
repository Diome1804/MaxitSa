<?php

namespace App\Core;

class YamlParser
{
    public static function parseFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: $filePath");
        }

        $content = file_get_contents($filePath);
        return self::parse($content);
    }

    public static function parse(string $yamlContent): array
    {
        $lines = explode("\n", $yamlContent);
        $result = [];
        $currentPath = [];
        
        foreach ($lines as $line) {
            // Ignorer les lignes vides et les commentaires
            $trimmed = trim($line);
            if (empty($trimmed) || $trimmed[0] === '#') {
                continue;
            }
            
            // Calculer l'indentation
            $indent = strlen($line) - strlen(ltrim($line));
            $level = intval($indent / 2); // Assumer 2 espaces par niveau
            
            // Ajuster le chemin actuel selon le niveau
            $currentPath = array_slice($currentPath, 0, $level);
            
            // Parser la ligne
            if (strpos($trimmed, ':') !== false) {
                [$key, $value] = explode(':', $trimmed, 2);
                $key = trim($key);
                $value = trim($value);
                
                if (empty($value)) {
                    // C'est une clé parent
                    $currentPath[] = $key;
                } else {
                    // C'est une valeur
                    $currentPath[] = $key;
                    self::setNestedValue($result, $currentPath, $value);
                    array_pop($currentPath); // Retirer la clé de valeur
                }
            }
        }
        
        return $result;
    }
    
    private static function setNestedValue(array &$array, array $path, $value): void
    {
        $current = &$array;
        
        foreach ($path as $key) {
            if (!isset($current[$key])) {
                $current[$key] = [];
            }
            $current = &$current[$key];
        }
        
        $current = $value;
    }
}
