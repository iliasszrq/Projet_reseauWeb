<?php

class PieceJointe extends Model
{
    protected $table = 'pieces_jointes';

    public function findByDemande(int $demandeId): array
    {
        $sql = "SELECT * FROM pieces_jointes 
                WHERE demande_id = :demande_id 
                ORDER BY created_at ASC";

        return $this->db->fetchAll($sql, ['demande_id' => $demandeId]);
    }

    public function countByDemande(int $demandeId): int
    {
        return $this->countWhere('demande_id', $demandeId);
    }

    public function uploader(int $demandeId, array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => $this->getUploadErrorMessage($file['error']),
                'id' => null
            ];
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            return [
                'success' => false,
                'message' => 'Le fichier dépasse la taille maximale autorisée (' . (MAX_FILE_SIZE / 1024 / 1024) . ' Mo).',
                'id' => null
            ];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            return [
                'success' => false,
                'message' => 'Extension non autorisée. Extensions acceptées : ' . implode(', ', ALLOWED_EXTENSIONS),
                'id' => null
            ];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!in_array($mimeType, $allowedMimes)) {
            return [
                'success' => false,
                'message' => 'Type de fichier non autorisé.',
                'id' => null
            ];
        }

        $uploadDir = UPLOAD_PATH . $demandeId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $nomFichier = uniqid() . '_' . time() . '.' . $extension;
        $cheminComplet = $uploadDir . $nomFichier;

        if (!move_uploaded_file($file['tmp_name'], $cheminComplet)) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du fichier.',
                'id' => null
            ];
        }

        $id = $this->create([
            'demande_id' => $demandeId,
            'nom_original' => $file['name'],
            'nom_fichier' => $nomFichier,
            'type_mime' => $mimeType,
            'taille' => $file['size'],
            'chemin' => $cheminComplet
        ]);

        return [
            'success' => true,
            'message' => 'Fichier uploadé avec succès.',
            'id' => $id
        ];
    }

    public function uploaderMultiple(int $demandeId, array $files): array
    {
        $resultats = [];

        $fileCount = count($files['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];

            $resultats[] = $this->uploader($demandeId, $file);
        }

        return $resultats;
    }

    public function supprimer(int $id): bool
    {
        $pieceJointe = $this->find($id);
        if (!$pieceJointe) {
            return false;
        }

        if (file_exists($pieceJointe['chemin'])) {
            unlink($pieceJointe['chemin']);
        }

        return $this->delete($id);
    }

    public function supprimerByDemande(int $demandeId): bool
    {
        $piecesJointes = $this->findByDemande($demandeId);

        foreach ($piecesJointes as $pj) {
            $this->supprimer($pj['id']);
        }

        $dossier = UPLOAD_PATH . $demandeId . '/';
        if (is_dir($dossier) && count(scandir($dossier)) === 2) {
            rmdir($dossier);
        }

        return true;
    }

    private function getUploadErrorMessage(int $errorCode): string
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximale autorisée par le serveur.',
            UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée par le formulaire.',
            UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement uploadé.',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été uploadé.',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
            UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture du fichier sur le disque.',
            UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté l\'upload.'
        ];

        return $messages[$errorCode] ?? 'Erreur inconnue lors de l\'upload.';
    }

    public static function formatTaille(int $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function getIcone(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'bi-file-image';
        } elseif ($mimeType === 'application/pdf') {
            return 'bi-file-pdf';
        } elseif (str_contains($mimeType, 'word')) {
            return 'bi-file-word';
        } else {
            return 'bi-file-earmark';
        }
    }

    public static function estImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }
}
