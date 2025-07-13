<?php

namespace App\Core;

class FileUpload
{
    private static FileUpload|null $instance = null;
    private string $uploadDir;

    public static function getInstance(): FileUpload
    {
        if (self::$instance === null) {
            self::$instance = new FileUpload();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../public/uploads/';
        
        // Créer le dossier uploads s'il n'existe pas
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload d'une image
     */
    public static function uploadImage(array $file, string $folder = 'images', array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']): array
    {
        try {
            $instance = self::getInstance();
            
            // Vérifier si le fichier a été uploadé sans erreur
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Erreur lors de l\'upload du fichier');
            }

            // Vérifier la taille du fichier (5MB max)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                throw new \Exception('Le fichier est trop volumineux (max 5MB)');
            }

            // Vérifier l'extension
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new \Exception('Extension de fichier non autorisée');
            }

            // Créer le dossier de destination
            $targetDir = $instance->uploadDir . $folder . '/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Générer un nom unique pour le fichier
            $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
            $targetPath = $targetDir . $fileName;

            // Déplacer le fichier
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return [
                    'success' => true,
                    'filename' => $fileName,
                    'path' => $targetPath,
                    'url' => '/uploads/' . $folder . '/' . $fileName
                ];
            } else {
                throw new \Exception('Impossible de déplacer le fichier');
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Supprimer un fichier
     */
    public static function deleteFile(string $filePath): bool
    {
        $fullPath = __DIR__ . '/../../public' . $filePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
